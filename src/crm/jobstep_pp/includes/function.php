<?php
//Error log enabled
ini_set('display_errors', 0);
ini_set('error_log', 'error_log');
ini_set('display_startup_errors', 1);
//error_reporting(0);
error_reporting(E_ALL);

//Connect to db
ob_start();
include("connect.php");
include("one-signal.php");
require_once("env.php");
date_default_timezone_set('Europe/Sarajevo');

	//User LogIn Session
	if(isset($_COOKIE['ppJobStepSession'])){

		Global $db;
		$user_key = $_COOKIE["ppJobStepSession"];

		$query = $db->prepare("
			SELECT 
				pu_id
			FROM 
				idk_pp_users
			WHERE 
				pu_key = :pu_key
		");

		$query->execute(array(
			':pu_key' => $user_key
		));

		$user = $query->fetch();

		Global $userId;
		$userId = $user['pu_id'];
	}else{
		$userId = 0;
	}
	
	function isLoggedInR(){
		Global $db;
		if(isset($_COOKIE['ppJobStepSession'])){
			$user_key = $_COOKIE["ppJobStepSession"];
			$query = $db->prepare("
				SELECT 
					pu_id
				FROM 
					idk_pp_users
				WHERE 
					pu_key = :pu_key
					AND 
					pu_status = 1
			");

			$query->execute(array(
				':pu_key' => $user_key
			));
			if($query->rowCount() != 0){
				//Uslov u kojem je ispunjen prethodni query i vraca se 1
				return 1;
			}else{
				// Uslov koji ce usera - ako mu se oznaci status 0 u bazi - vratiti ga na login sa bilo kojeg mjesta u sistemu
				// Zbog toga se unset-uje cookie a obzirom da je aktiviran - nece se vise moci logirati
				setcookie("ppJobStepSession", "", time() - 60 * 60 * 24 * 30);
				unset($_COOKIE["ppJobStepSession"]);
				return 0;
			}
		}else{
			return 0;
		}
	}
	
	function addToLogs($log_desc){
		Global $db;
		Global $userId;
		$log_date = date('Y-m-d H:i:s');
		
		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date, log_type)
						VALUES
							(:log_employeeid, :log_desc, :log_date, :log_type)");

		$log_query->execute(array(
						':log_employeeid' => $userId,
						':log_desc' => $log_desc,
						':log_date' => $log_date,
						':log_type' => 10));

		//Log za PP - $log_type = 10
	}
	
	function getLanguageForUser($userId){
		Global $db;
		$query = $db->prepare("
			SELECT
				pu_language
			FROM 
				idk_pp_users
			WHERE 
				pu_id = :pu_id
		");
		$query->execute(array(
			':pu_id' => $userId
		));
		$row = $query->fetch();
		$language = intval($row["pu_language"]);
		
		return $language;
	}

	function checkUserIsJobStepEmployee() {
		Global $db; 
		Global $userId; 
		$result = 0;
		if ($userId != 0) {
			$query = $db->prepare("
				SELECT 
					pu_email 
				FROM 
					idk_pp_users
				WHERE 
					pu_id = :pu_id
					AND 
					pu_status = 1
			");
			$query->execute(array(
				':pu_id' => $userId
			));
			if ($query->rowCount() == 1) {
				$row = $query->fetch();
				$email = $row['pu_email'];
				$domain = '@job-step.com';
				if (substr($email, -strlen($domain)) === $domain) {
					$result = 1;
				}
			}
		}

		return $result; 
	}
	
	function getLogoPathR() {
		Global $envConfig;
		/*
			APP_ENV = "dev"
			APP_ENV = "staging"
			APP_ENV = "production"
		*/ 
		$app_env = $envConfig->APP_ENV;
		$logo_path = "images/logo/jobsoft_logo.png"; 
		if($app_env != "production"){
			// If development environment set logo path for dev, else set logo path for staging
			if($app_env == 'dev'){
				$logo_path = "images/logo/jobsoft_logo_dev.png";
			}else{
				$logo_path = "images/logo/jobsoft_logo_staging.png";
			}
		}
		return $logo_path;
	}

	//Site url echo
	function getSiteUrl() {
    Global $envConfig;
    echo $envConfig->JS_URL;
	}

	//Site url return
	function getSiteUrlr() {
    Global $envConfig;
    return $envConfig->JS_URL;
	}

	function getCRMUrl() {
    Global $envConfig;
    echo $envConfig->CRM_URL;
	}

	function getCRMUrlr() {
    Global $envConfig;
    return $envConfig->CRM_URL;
	}
	
	function getFirstAndLastNameUserR($id){
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

	function getEmployeeFullNameById($id) {
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
		$firstnameUser = $row["employee_firstname"];
		$lastnameUser = $row["employee_lastname"];
		return $firstnameUser." ".$lastnameUser;
	}
	
	function getFirstNameUserR($id){
		Global $db;
		$query = $db->prepare("
			SELECT pu_fname
			FROM idk_pp_users
			WHERE pu_id = :pu_id
		");
		$query->execute(array(
			':pu_id' => $id
		));
		$row = $query->fetch();
		$firstnameUser = $row["pu_fname"];
		return $firstnameUser;
	}
	
	function getUserCompanyIdR($userIdVr){
		Global $db;
		
		$userId = intval($userIdVr);
		$companyId = 0;
		if($userId != 0){
			$query = $db->prepare("
				SELECT 
					pu_company_id
				FROM 
					idk_pp_users
				WHERE 
					pu_id = :pu_id
			");
			$query->execute(array(
				':pu_id' => $userId
			));
			$row = $query->fetch();
			$companyId = intval($row["pu_company_id"]);
			return $companyId;
		}else{
			return 0;
		}
	}
	
	function getAktivniNaloziKompanijeArrayIdR($companyIdVr){
		Global $db;
		$companyId = intval($companyIdVr);
		$naloziId = array();
		if($companyId != 0){
			$cntNalog = 0;
			$query = $db->prepare("
				SELECT 
					nalog_id
				FROM 
					idk_nalozi
				WHERE 
					kompanija_id = :kompanija_id
					AND 
					nalog_status != 12
			");
			$query->execute(array(
				':kompanija_id' => $companyId
			));
			$cntNalog = $query->rowCount();
			if($cntNalog != 0){
				while($row = $query->fetch()){
					$nalogId = intval($row["nalog_id"]);
					array_push($naloziId, $nalogId);
				}
				return $naloziId;
			}else{
				return $naloziId;
			}
		}else{
			return $naloziId;
		}
		unset($naloziId);
	}
	
	function getPartneriNalogaArrayIdR($nalogIdVr){
		Global $db;
		$nalogId = intval($nalogIdVr);
		$rezId = array();
		if($nalogId != 0){
			$cntQuery = 0;
			$query = $db->prepare("
				SELECT 
					ppa_id
				FROM 
					idk_pp_partners
				WHERE 
					ppa_nalog_id = :ppa_nalog_id
			");
			$query->execute(array(
				':ppa_nalog_id' => $nalogId
			));
			$cntQuery = $query->rowCount();
			if($cntQuery != 0){
				while($row = $query->fetch()){
					$partnerId = intval($row["ppa_id"]);
					array_push($rezId, $partnerId);
				}
				return $rezId;
			}else{
				return $rezId;
			}
		}else{
			return $rezId;
		}
	}

	function getPartnerForCandidate($idCand){
		Global $db; 
		$idCand = intval($idCand);
		if ( $idCand != 0 ) {

			$query = $db->prepare("
				SELECT 
					kandidat_ppa_partner_id 
				FROM 
					idk_kandidati 
				WHERE 
					kandidat_id = :kandidat_id 
			");
			$query->execute(array(
				':kandidat_id' => $idCand
			));

			$row = $query->fetch();

			if ( $row["kandidat_ppa_partner_id"] != NULL ) {

				return intval($row["kandidat_ppa_partner_id"]); 

			}else {

				return 0;

			}

		} else {

			return 0;

		}
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
				if($nalogId == 213){
					$nalogNaziv = str_replace("25","", $row["nalog_naziv"]);
				}
				else if($nalogId == 211){
					$nalogNaziv = str_replace("55","", $row["nalog_naziv"]);
				}else{
					$nalogNaziv = $row["nalog_naziv"];
				}
				// return setNameForDenis($nalogNaziv); 
				return $nalogNaziv; 
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
	
	function getPermissionValueR($permissionIdVr){
		Global $db;
		$permissionId = intval($permissionIdVr);
		$permissionValue = "";
		if($permissionId != 0){
			$cntQuery = 0;
			$query = $db->prepare("
				SELECT 
					ppe_menu
				FROM 
					idk_pp_permissions 
				WHERE
					ppe_id = :ppe_id
			");
			$query->execute(array(
				':ppe_id' => $permissionId
			));
			$cntQuery = $query->rowCount();
			if($cntQuery != 0){
				$row = $query->fetch();
				$permissionValue = $row["ppe_menu"];
				return $permissionValue;
			}else{
				return "Undefined";
			}
		}else{
			return "Undefined";
		}
	}
	
	function getCheckPartnersR($nalogIdVr){
		Global $db;
		$nalogId = intval($nalogIdVr);
		if($nalogId != 0){
			$query = $db->prepare("
				SELECT 
					partneri_pp
				FROM 
					idk_nalozi
				WHERE 
					nalog_id = :nalog_id
			");
			$query->execute(array(
				':nalog_id' => $nalogId
			));
			$row = $query->fetch();
			return intval($row["partneri_pp"]);
		}else{
			return "Undefined";
		}
	}
	
	function getUserAccess($userIdVr){
		Global $db;
		$userId = $userIdVr;
		$userAccess = array(
			"superAdminAccess" => array(
				"count" => array(),
				"nalog" => array(),
				"permission" => array()
			),
			"adminAccess" => array(
				"count" => array(),
				"nalog" => array(),
				"partner" => array(),
				"permission" => array()
			)
		);
		if($userId != 0){
			$cntSuperAdminQuery = 0;
			$cntAdminQuery = 0;
			$query = $db->prepare("
				SELECT 
					pua_id, pua_type, pua_partner_id, pua_nalog_id, pua_permission_id
				FROM 
					idk_pp_user_access
				WHERE 
					pua_user_id = :pua_user_id AND pua_status = 1
				ORDER BY pua_type, pua_id, pua_nalog_id, pua_partner_id ASC
			");
			$query->execute(array(
				':pua_user_id' => $userId
			));
			$cntQuery = $query->rowCount();
			if($cntQuery != 0){
				while($row = $query->fetch()){
					$puaId = intval($row["pua_id"]);
					$puaType = intval($row["pua_type"]);
					$puaPartnerId = intval($row["pua_partner_id"]);
					$puaNalogId = intval($row["pua_nalog_id"]);
					$puaPermissionId = intval($row["pua_permission_id"]);
					
					if($puaType == 1){
						array_push($userAccess["superAdminAccess"]["count"], $cntSuperAdminQuery);
						array_push($userAccess["superAdminAccess"]["nalog"], $puaNalogId);
						array_push($userAccess["superAdminAccess"]["permission"], $puaPermissionId);
						$cntSuperAdminQuery++;
					}else{
						array_push($userAccess["adminAccess"]["count"], $cntAdminQuery);
						array_push($userAccess["adminAccess"]["nalog"], $puaNalogId);
						array_push($userAccess["adminAccess"]["partner"], $puaPartnerId);
						array_push($userAccess["adminAccess"]["permission"], $puaPermissionId);
						$cntAdminQuery++;
					}
				}
				return $userAccess;
			}else{
				return $userAccess;
			}
		}else{
			return $userAccess;
		}
		
		unset($userAccess);
	}
	
	function getPositionElementInArrayR($nalogIdVr, $array){
		$nalogId = intval($nalogIdVr);
		$rezult = array();
		if($nalogId != 0 AND count($array) != 0){
			$count = 0;
			foreach($array AS $val){
				if($nalogId == $val){
					array_push($rezult, $count);
				}
				$count++;
			}
			return $rezult; 
		}else{
			return $rezult; 
		}
		unset($rezult);
	}
	
	// FUNKCIJA KOJA VRAĆA SVE ZAVRŠENE ŠKOLE KANDIDATA U JEDNOM STRINGU __ START _____________________________________________
	function getCandidateEducation($candidate_id, $lang){
		if($lang == 'bh'){
			$education_sufix = "";
			$negative_return = "Nije poznata";
		}
		else if($lang == 'de'){
			$education_sufix = "_de";
			$negative_return = "Nicht bekannt";
		}
		Global $db;
		
		$query_get_candidate_education = $db -> prepare('
			SELECT ke_naziv_kvalifikacije'.$education_sufix.' 
			FROM idk_kandidat_edukacija
			WHERE ke_kandidat_id = :candidate_id
			AND ke_skola_id IS NOT NULL
			AND ke_naziv_kvalifikacije'.$education_sufix.' IS NOT NULL
		');
		$query_get_candidate_education -> execute(array(':candidate_id' => $candidate_id));
		
		$candidate_education = array();
		while($row_get_candidate_education = $query_get_candidate_education -> fetch()){
			array_push($candidate_education, $row_get_candidate_education['ke_naziv_kvalifikacije'.$education_sufix]);
		}
		
		$candidate_education = implode(', ', $candidate_education);
		
		if($candidate_education == ''){
			$candidate_education = $negative_return;
		}

		return $candidate_education;
	}
	// FUNKCIJA KOJA VRAĆA SVE ZAVRŠENE ŠKOLE KANDIDATA U JEDNOM STRINGU __ END _______________________________________________
	
	// FUNKCIJA KOJA VRAĆA TRUE AKO KANDIDAT IMA ZAVRŠENO VISOKO OBRAZOVANJE, FALSE AKO NE __ START ___________________________
	function checkCandidateHigherEducation($kandidat_id){
		Global $db;
		$query_get_candidate_higher_education = $db -> prepare('
			SELECT count(ke_vrsta_obrazovanja) as cnt
			FROM idk_kandidat_edukacija
			WHERE ke_kandidat_id = :kandidat_id
		');
		
		$query_get_candidate_higher_education -> execute(array(':kandidat_id' => $kandidat_id));
		
		$row_get_candidate_higher_education = $query_get_candidate_higher_education -> fetch();
		
		$cnt = $row_get_candidate_higher_education['cnt'];
		$return_value = false;
		if($cnt > 0)
			$return_value = true;
		
		return $return_value;
	}
	//FUNKCIJA KOJA VRAĆA TRUE AKO KANDIDAT IMA ZAVRŠENO VISOKO OBRAZOVANJE, FALSE AKO NE __ END _____________________________
		
	/* FUNKCIJA KOJA VRAĆA BROJ KANDIDATA PO PROJEKTU/STASTUSU JEDNOG NALOGA ZA PIE CHART __ START_____________________________
	
		user_type = 1, partner_id NULL 	-> Superadmin projekti
		user_type = 1, partner_id = 0	-> Superadmin projekti + svi statusi prijave skupa (POSTOJI NOTICE)
		user_type = 2, partner_id => 0 	-> Adminovi statusi prijave
	*/
	function getCandidatesForPieChart($nalog_id, $user_type, $partner_id = NULL){
		Global $db;
		$return_array = array();
		
		if($user_type == 1 AND (is_null($partner_id) OR $partner_id == 0)){
			
			$query_get_candidates_for_pie = $db -> prepare("
				SELECT 
					SUM(
						CASE
							WHEN pr.project_name LIKE ('%Obra%')
							THEN 1
							ELSE 0
						END
					) AS broj_obradjenih,
					SUM(
						CASE
							WHEN pr.project_name LIKE ('%asting%')
							THEN 1
							ELSE 0
						END
					) AS broj_casting
				FROM (
					SELECT sq_pr.project_name
					FROM idk_projects sq_pr
					JOIN idk_project_kandidati sq_pk 
					ON sq_pk.pk_projectid = sq_pr.project_id
					JOIN idk_kandidati sq_kan
					ON sq_kan.kandidat_id = sq_pk.pk_kandidatid
					WHERE sq_pr.project_nalogid = :nalog_id
					AND sq_kan.kandidat_status != 3
				) pr
			");
			
			$query_get_candidates_for_pie -> execute(array(':nalog_id' => $nalog_id));
			
			$row_get_candidates_for_pie = $query_get_candidates_for_pie->fetch();
			
			array_push($return_array, $row_get_candidates_for_pie['broj_obradjenih'], $row_get_candidates_for_pie['broj_casting']);
			if($partner_id == '0'){
				// NOTICE -> OVDJE DODATI PREBROJAVANJE ZA NEKOGA KO JE SUPER ADMIN I ADMIN ISTOVREMENO
				array_push($return_array, '10', '40', '10', '13', '8');
			}
		}
		else if($user_type == 2){
			// NOTICE -> OVDJE DODATI PREBROJAVANJE ZA ADMINA
			array_push($return_array, '20', '40', '10', '13', '8');
		}
		
		return $return_array;
	}
	// FUNKCIJA KOJA VRAĆA BROJ KANDIDATA PO PROJEKTU/STASTUSU JEDNOG NALOGA ZA PIE CHART __ END ______________________________
	
	/*FUNKCIJA KOJA VRAĆA KRITERIJ NALOGA __ START ____________________________________________________________________________
	
		[0] - Da li je vozacka trazena na linku 	[block (jeste) / none (nije)]
		[1] - Koja se vozacka kategorija traži 		[vozacka kategorija / NULL]
		[2] - Koji nivo njemačkog jezika se traži 	[BZ do C2]
		[3] - Da li se traži radno iskustvo 		[1 (traži) / 0 (ne traži)]
		[4] - Da li se traži visoko obrazovanje 	[1 (traži) / 0 (ne trazi)]
	*/
	function getNalogKriterij($nalog_id){
		Global $db;
		
		$return_array = array();
		
		$query_get_nalog_kriterij = $db -> prepare('
			SELECT nbp_vozacka, nbp_vozacka_kategorija, nbp_korak4, nbp_njemacki_jezik, nbp_visoko_obr 
			FROM idk_nalozi_blokovi_prijave 
			WHERE nbp_nalogid = :nalog_id
		');
		
		$query_get_nalog_kriterij -> execute(array(':nalog_id' => $nalog_id));
		
		$row_get_nalog_kriterij = $query_get_nalog_kriterij -> fetch();
		array_push($return_array, 
			$row_get_nalog_kriterij['nbp_vozacka'], 
			$row_get_nalog_kriterij['nbp_vozacka_kategorija'], 
			$row_get_nalog_kriterij['nbp_njemacki_jezik'], 
			$row_get_nalog_kriterij['nbp_korak4'],
			$row_get_nalog_kriterij['nbp_visoko_obr']
		);
		
		return $return_array;
	}
	//FUNKCIJA KOJA VRAĆA KRITERIJ NALOG __ END__________________________________________________________________________________
	
	//FUNKCIJA KOJA VRAĆA RADNO ISKUSTVO KANDIDATA (pozicija, naziv, godine) ZA ISPIS __ START __________________________________
	function getCandidateExperience($kandidat_id, $lang){
		Global $db;
		
		$query_get_candidate_experience = $db -> prepare('
			SELECT kri_pozicija, kri_pozicija_de, kri_naziv, kri_naziv_de, ROUND(DATEDIFF(kri_datum_do, kri_darum_od)/365,0) as years
			FROM idk_kandidat_radno_iskustvo
			WHERE kri_kandidat_id = :kandidat_id
		');
		
		$query_get_candidate_experience -> execute(array(':kandidat_id' => $kandidat_id));
		
		$return_array = array();
		
		while($row_get_candidate_experience = $query_get_candidate_experience -> fetch()){
			$kri_pozicija 		= $row_get_candidate_experience['kri_pozicija'];
			$kri_naziv 			= $row_get_candidate_experience['kri_naziv'];
			$years 				= $row_get_candidate_experience['years'];
			$kri_pozicija_de 	= $row_get_candidate_experience['kri_pozicija_de'];
			$kri_naziv_de 		= $row_get_candidate_experience['kri_naziv_de'];
			
			if($years == 0)
				$years = 1;
			
			if($lang == 'bs')
				array_push($return_array, 'Kao '.$kri_pozicija.' u '.$kri_naziv.' '.$years.' godina.');
			else if($lang == 'de')
				array_push($return_array, 'Als '.$kri_pozicija.' u '.$kri_naziv.' '.$years.' Jahre.');
			
		}
			
		$return_array = implode('|', $return_array);
		
		if($return_array == "")
			$return_array = "Nije poznato";
		
		return $return_array;
	}
	//FUNKCIJA KOJA VRAĆA RADNO ISKUSTVO KANDIDATA (pozicija, naziv, godine) ZA ISPIS __ END ____________________________________

	function getUserTypeR($userId){
		$userId = intval($userId);
		if($userId != 0){
			Global $db;
			$query = $db->prepare("
				SELECT 
					SUM(CASE WHEN pua_type = 1 THEN 1 ELSE 0 END) AS superAdminSum,
					SUM(CASE WHEN pua_type = 2 THEN 1 ELSE 0 END) AS adminSum
				FROM 
					idk_pp_user_access
				WHERE 
					pua_user_id = :pua_user_id
					AND 
					pua_status = 1
				ORDER BY pua_id DESC
			");
			$query->execute(array(
				':pua_user_id' => $userId
			));
			$row = $query->fetch();
			$superAdminSum = intval($row["superAdminSum"]);
			$adminSum = intval($row["adminSum"]);
			if($superAdminSum > 0){
				return 1;				
			}else if($adminSum > 0 AND $superAdminSum == 0){
				return 2;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
	
	function getMaxNalogOfUser($userId, $userType){
		$userId = intval($userId);
		$userType = intval($userType); // 1 - SuperAdmin 2 - Admin
		if($userId != 0){
			Global $db;
			$query = $db->prepare("
				SELECT 
					pua_nalog_id AS nalogId
				FROM 
					idk_pp_user_access
				WHERE 
					pua_user_id = :pua_user_id
					AND 
					pua_type = :pua_type
					AND 
					pua_status = 1
				ORDER BY pua_id ASC
			");
			$query->execute(array(
				':pua_user_id' => $userId,
				':pua_type' => $userType
			));
			if($query->rowCount() != 0){
				$row = $query->fetch();
				$nalogId = intval($row["nalogId"]);
				return $nalogId;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
	
	function getMaxPartnerOfUser($userId, $nalogId){
		$userId = intval($userId);
		$nalogId = intval($nalogId); 
		if($userId != 0){
			Global $db;
			$query = $db->prepare("
				SELECT 
					pua_partner_id AS parnerId
				FROM 
					idk_pp_user_access
				WHERE 
					pua_user_id = :pua_user_id
					AND 
					pua_type = 2
					AND 
					pua_status = 1
					AND 
					pua_nalog_id = :pua_nalog_id
				ORDER BY pua_id ASC
			");
			$query->execute(array(
				':pua_user_id' => $userId,
				':pua_nalog_id' => $nalogId
			));
			if($query->rowCount() != 0){
				$row = $query->fetch();
				$parnerId = intval($row["parnerId"]);
				return $parnerId;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
	
	function getCandidatAppointmentsArrayR($candidatId, $nalogIdd){
		Global $db;
		$candidatId = intval($candidatId);
		$nalogId = $nalogIdd;
		// var_dump($candidatId);
		// var_dump($nalogId);
		//$nalogId = 171;
		$arrayResult = array(
			"count" => array(),
			"pap_id" => array(),
			"pap_date" => array(),
			"pap_city" => array(),
			"pca_id" => array(),
			"pca_time" => array(),
			"pca_avg_rating" => array(), 
			"pca_status" => array()
		);
			// var_dump('uso');
		if($candidatId != 0 AND $nalogId != 0){
			$query = $db->prepare("
				SELECT 
					pap.pap_id, pap.pap_date, pap.pap_city, pca.pca_id, pca.pca_time, pca.pca_avg_rating, pca.pca_status
				FROM 
					idk_pp_appointments pap
				INNER JOIN
					idk_pp_cand_appts pca
				ON 
					pap.pap_id = pca.pca_appointment_id
				WHERE 
					pap.pap_nalog_id = :pap_nalog_id
					AND 
					pca.pca_kandidat_id = :pca_kandidat_id
			");
			// var_dump('uso');
			$query->execute(array(
				':pca_kandidat_id' => $candidatId,
				':pap_nalog_id' => $nalogId
			));
			if($query->rowCount() != 0){
				$cnt = 0;
				while($row = $query->fetch()){
					$pap_id = intval($row["pap_id"]);
					$pap_date = date("d.m.Y", strtotime($row["pap_date"]));
					$pap_city = $row["pap_city"];
					$pca_id = intval($row["pca_id"]);
					$pca_time = date("H:i", strtotime($row["pca_time"]));
					$pca_avg_rating = $row["pca_avg_rating"];
					$pca_status = intval($row["pca_status"]);
					if($pca_avg_rating != NULL){
						$pca_avg_rating = $pca_avg_rating;
					}else{
						$pca_avg_rating = "noRating";
					}
					array_push($arrayResult["count"], $cnt);
					array_push($arrayResult["pap_id"], $pap_id);
					array_push($arrayResult["pap_date"], $pap_date);
					array_push($arrayResult["pap_city"], $pap_city);
					array_push($arrayResult["pca_id"], $pca_id);
					array_push($arrayResult["pca_time"], $pca_time);
					array_push($arrayResult["pca_avg_rating"], $pca_avg_rating);
					array_push($arrayResult["pca_status"], $pca_status);
					
					$cnt++;
				}
				return $arrayResult;
			}else{
				return $arrayResult;
			}
		}else{
			return $arrayResult;
		}
		unset($arrayResult);
	}
	
	function archiveCandidateAppointment($canId, $nalogId){
		Global $db; 
		Global $userId; 
		$canId = intval($canId);
		$nalogId = intval($nalogId);

		$queryCheck = $db->prepare("
			SELECT 
				pca.pca_id
			FROM 
				idk_pp_appointments pap
			INNER JOIN
				idk_pp_cand_appts pca
			ON 
				pap.pap_id = pca.pca_appointment_id
			WHERE 
				pap.pap_nalog_id = :pap_nalog_id
				AND 
				pca.pca_kandidat_id = :pca_kandidat_id 
				AND
				pca.pca_status = 1
		");
		$queryCheck->execute(array(
			":pap_nalog_id" => $nalogId, 
			":pca_kandidat_id" => $canId
		));
		if($queryCheck->rowCount() == 1){
			$rowCheck = $queryCheck->fetch();
			$pca_id = intval($rowCheck["pca_id"]);
			$queryUpdateStatus = $db->prepare("
				UPDATE 
					idk_pp_cand_appts
				SET 
					pca_status = 0
				WHERE 
					pca_id = :pca_id
			");
			$queryUpdateStatus->execute(array(
				":pca_id" => $pca_id
			));
			$logDesc = "Jobstep PP: Arhiviran termin ID = [".$pca_id."] kod kandidata ID = [".$canId."] na nalogu ID = [".$nalogId."]";
			addToLogs($logDesc);
		}
	}

	function getAppointmentQuestionForAppointmentArrayR($pap_id){
		Global $db;
		Global $userId;

		$lang = getLanguageForUser($userId);

		$pap_id = intval($pap_id);

		$resultArray = array(
			"count" => array(),
			"papq_id" => array(),
			"pqu_id" => array(),
			"pqu_question" => array(),
			"pqu_has_text" => array(),
			"pqu_has_rating" => array(),
			"pqu_has_dropdown" => array(),
			"categoryStart" => array(),
			"categoryId" => array(),
			"categoryName" => array()
		);

		if($pap_id != 0){

			$categoryArray = array(
				"categoryId" => array(),
				"categoryIsSet" => array()
			);
			
			$queryCategory = $db->prepare("
				SELECT 
					pqu.pqu_category_id
				FROM 
					idk_pp_appointments_questions papq
				INNER JOIN 
					idk_pp_questions pqu
				ON 
					papq.papq_question_id = pqu.pqu_id
				WHERE 
					papq.papq_appointment_id = :papq_appointment_id
				GROUP BY 
					pqu.pqu_category_id
				ORDER BY 
					pqu.pqu_category_id ASC
			");
			$queryCategory->execute(array(
				':papq_appointment_id' => $pap_id
			));
			while($rowCategory = $queryCategory->fetch()){
				$category_id = intval($rowCategory["pqu_category_id"]);
				array_push($categoryArray["categoryId"], $category_id);
				array_push($categoryArray["categoryIsSet"], 0);
			}
			
			$langCategory = '';
			
			if(in_array($lang, array(0,2))){
				$langCategory = 'pqc_name';
			}else{
				$langCategory = 'pqc_name_de';
			}

			$query = $db->prepare("
				SELECT 
					papq.papq_id, pqu.pqu_id, pqu.pqu_question, pqu.pqu_has_text, pqu.pqu_has_rating, pqu.pqu_has_dropdown, pqu.pqu_category_id, pqc.".$langCategory." AS pqcName
				FROM 
					idk_pp_appointments_questions papq
				INNER JOIN 
					idk_pp_questions pqu
				ON 
					papq.papq_question_id = pqu.pqu_id
				LEFT JOIN 
					idk_pp_question_categories pqc
				ON 
					pqu.pqu_category_id = pqc.pqc_id
				WHERE 
					papq.papq_appointment_id = :papq_appointment_id
				ORDER BY 
					papq.papq_order ASC, papq.papq_id ASC
			");
			$query->execute(array(
				':papq_appointment_id' => $pap_id
			));
			if($query->rowCount() != 0){
				$cnt = 0;
				while($row = $query->fetch()){
					$papq_id = intval($row["papq_id"]);
					$pqu_id = intval($row["pqu_id"]);
					$pqu_question = $row["pqu_question"];
					$pqu_has_text = $row["pqu_has_text"];
					$pqu_has_rating = $row["pqu_has_rating"];
					$pqu_has_dropdown = $row["pqu_has_dropdown"];
					$pqu_category_id = intval($row["pqu_category_id"]);
					$pqc_name = $row["pqcName"];

					if(in_array($pqu_category_id, $categoryArray["categoryId"])){

						$positionInCategoryArray = array_search($pqu_category_id, $categoryArray["categoryId"]);

						array_push($resultArray["count"], $cnt);
						array_push($resultArray["papq_id"], $papq_id);
						array_push($resultArray["pqu_id"], $pqu_id);
						array_push($resultArray["pqu_question"], $pqu_question);
						array_push($resultArray["pqu_has_text"], $pqu_has_text);
						array_push($resultArray["pqu_has_rating"], $pqu_has_rating);
						array_push($resultArray["pqu_has_dropdown"], $pqu_has_dropdown);
						
						if($categoryArray["categoryIsSet"][$positionInCategoryArray] == 0){
							$categoryArray["categoryIsSet"][$positionInCategoryArray] = 1;
							array_push($resultArray["categoryStart"], 1);
							array_push($resultArray["categoryId"], $pqu_category_id);
							array_push($resultArray["categoryName"], $pqc_name);
						}else{
							array_push($resultArray["categoryStart"], 0);
							array_push($resultArray["categoryId"], $pqu_category_id);
							array_push($resultArray["categoryName"], $pqc_name);
						}

						$cnt++;
					}
				}
				return $resultArray;
			}else{
				return $resultArray;
			}
		}else{
			return $resultArray;
		}
	}

	function getAppointmentQuestionForAppointmentEnpalArrayR($pap_id) {
		Global $db;
		Global $userId;

		$lang = getLanguageForUser($userId);

		$pap_id = intval($pap_id);

		$resultArray = array(
			"count" => array(),
			"papq_id" => array(),
			"pqu_id" => array(),
			"pqu_question" => array(),
			"pqu_has_text" => array(),
			"pqu_has_rating" => array(),
			"pqu_has_dropdown" => array(),
			"categoryStart" => array(),
			"categoryId" => array(),
			"categoryName" => array()
		);

		if($pap_id != 0){

			$categoryArray = array(
				"categoryId" => array(),
				"categoryIsSet" => array()
			);

			$queryCategory = $db->prepare("
				SELECT 
					pqu.pqu_category_id
				FROM 
					idk_pp_appointments_questions papq
				INNER JOIN 
					idk_pp_questions pqu
				ON 
					papq.papq_question_id = pqu.pqu_id
				WHERE 
					papq.papq_appointment_id = :papq_appointment_id
				GROUP BY 
					pqu.pqu_category_id
				ORDER BY 
					pqu.pqu_category_id ASC
			");
			$queryCategory->execute(array(
				':papq_appointment_id' => $pap_id
			));

			while($rowCategory = $queryCategory->fetch()){
				$category_id = intval($rowCategory["pqu_category_id"]);
				array_push($categoryArray["categoryId"], $category_id);
				array_push($categoryArray["categoryIsSet"], 0);
			}

			$langCategory = '';
			
			if(in_array($lang, array(0,2))){
				$langCategory = 'pqc_name';
			}else{
				$langCategory = 'pqc_name_de';
			}

			$query = $db->prepare("
				SELECT 
					papq.papq_id, pqu.pqu_id, pqu.pqu_question, pqu.pqu_has_text, pqu.pqu_has_rating, pqu.pqu_has_dropdown, pqu.pqu_category_id, pqc.".$langCategory." AS pqcName
				FROM 
					idk_pp_appointments_questions papq
				INNER JOIN 
					idk_pp_questions pqu
				ON 
					papq.papq_question_id = pqu.pqu_id
				LEFT JOIN 
					idk_pp_question_categories pqc
				ON 
					pqu.pqu_category_id = pqc.pqc_id
				WHERE 
					papq.papq_appointment_id = :papq_appointment_id
				ORDER BY 
					pqu.pqu_category_id ASC, papq.papq_id ASC
			");
			$query->execute(array(
				':papq_appointment_id' => $pap_id
			));
			if($query->rowCount() != 0){
				$cnt = 0;
				while($row = $query->fetch()){
					$papq_id = intval($row["papq_id"]);
					$pqu_id = intval($row["pqu_id"]);
					$pqu_question = $row["pqu_question"];
					$pqu_has_text = $row["pqu_has_text"];
					$pqu_has_rating = $row["pqu_has_rating"];
					$pqu_has_dropdown = $row["pqu_has_dropdown"];
					$pqu_category_id = intval($row["pqu_category_id"]);
					$pqc_name = $row["pqcName"];

					if(in_array($pqu_category_id, $categoryArray["categoryId"])){

						$positionInCategoryArray = array_search($pqu_category_id, $categoryArray["categoryId"]);

						array_push($resultArray["count"], $cnt);
						array_push($resultArray["papq_id"], $papq_id);
						array_push($resultArray["pqu_id"], $pqu_id);
						array_push($resultArray["pqu_question"], $pqu_question);
						array_push($resultArray["pqu_has_text"], $pqu_has_text);
						array_push($resultArray["pqu_has_rating"], $pqu_has_rating);
						array_push($resultArray["pqu_has_dropdown"], $pqu_has_dropdown);
						
						if($categoryArray["categoryIsSet"][$positionInCategoryArray] == 0){
							$categoryArray["categoryIsSet"][$positionInCategoryArray] = 1;
							array_push($resultArray["categoryStart"], 1);
							array_push($resultArray["categoryId"], $pqu_category_id);
							array_push($resultArray["categoryName"], $pqc_name);
						}else{
							array_push($resultArray["categoryStart"], 0);
							array_push($resultArray["categoryId"], $pqu_category_id);
							array_push($resultArray["categoryName"], $pqc_name);
						}

						$cnt++;
					}
				}
				return $resultArray;
			}else{
				return $resultArray;
			}
		}else{
			return $resultArray;
		}
	}

	function getQuestionOptionsArrayR($pqu_id){
		Global $db; 
		Global $userId;

		$lang = getLanguageForUser($userId);

		$pqu_id = intval($pqu_id); 

		$result = array(
			"count" => array(),
			"pqo_id" => array(),
			"pqo_value" => array(),
			"pqo_value_text" => array(),
			"pqo_value_subtext" => array()
		);

		$valueTextColumn = "";
		$valueSubtextColumn = "";
			
		if(in_array($lang, array(0,2))){
			$valueTextColumn = "pqo_value_text";
			$valueSubtextColumn = "pqo_value_subtext";
		}else{
			$valueTextColumn = "pqo_value_text_de";
			$valueSubtextColumn = "pqo_value_subtext_de";
		}

		if ($pqu_id != 0){
			$query = $db->prepare("
				SELECT 
					pqo_id AS pqoId, 
					pqo_value AS pqoValue, 
					".$valueTextColumn." AS pqoValueText, 
					".$valueSubtextColumn." AS pqoValueSubtext
				FROM 
					idk_pp_question_options
				WHERE 
					pqo_question_id = :pqo_question_id
				ORDER BY 
					pqo_id ASC
			");
			$query->execute(array(
				':pqo_question_id' => $pqu_id
			));
			
			if($query->rowCount() != 0){
				$cnt = 0;
				while($row = $query->fetch()){
					$pqoId = intval($row["pqoId"]);
					$pqoValue = intval($row["pqoValue"]);
					$pqoValueText = $row["pqoValueText"];
					$pqoValueSubtext = $row["pqoValueSubtext"];

					array_push($result["count"], $cnt);
					array_push($result["pqo_id"], $pqoId);
					array_push($result["pqo_value"], $pqoValue);
					array_push($result["pqo_value_text"], $pqoValueText);
					array_push($result["pqo_value_subtext"], $pqoValueSubtext);

					$cnt++;
				}
			}
		} 

		return $result;
	}

	function valueCheckForCandidateQuestion($candidateIdVar, $papqIdVar, $columnNameVar) {

		Global $db; 

		$candidateIdVar = intval($candidateIdVar);
		$papqIdVar = intval($papqIdVar);
		$columnNameVar = $columnNameVar; 

		if ($columnNameVar == "pra_rating") {
			$defaultValue = 0;
		} else if ($columnNameVar == "pra_options_value") {
			$defaultValue = 0;
		} else {
			$defaultValue = "";
		}

		$result = array(
			"status" => 101,
			"value" => $defaultValue
		);

		$query = $db->prepare("
			SELECT 
				".$columnNameVar." AS valueColumn
			FROM 
				idk_pp_ratings
			WHERE 
				pra_kandidat_id = :pra_kandidat_id
				AND 
				pra_appointment_question_id = :pra_appointment_question_id
				AND
				pra_id = (
					SELECT 
						MAX(ppr.pra_id)
					FROM 
						idk_pp_ratings ppr
					WHERE 
						ppr.pra_kandidat_id = :pra_kandidat_id
						AND 
						ppr.pra_appointment_question_id = :pra_appointment_question_id
				)
		");
		$query->execute(array(
			':pra_kandidat_id' => $candidateIdVar, 
			':pra_appointment_question_id' => $papqIdVar
		));
		if($query->rowCount() == 1){
			$row = $query->fetch();
			$valueColumn = $row["valueColumn"]; 
			if ($columnNameVar == "pra_rating") {
				$valueColumn = intval($valueColumn);
				$result["status"] = (($valueColumn != 0) ? 1 : 0);
				$result["value"] = $valueColumn;
			} else if ($columnNameVar == "pra_options_value") {
				$valueColumn = intval($valueColumn);
				$result["status"] = (($valueColumn != 0) ? 1 : 0);
				$result["value"] = $valueColumn;
			} else {
				$valueColumn = $valueColumn;
				$result["status"] = (($valueColumn != NULL) ? 1 : 0);
				$result["value"] = $valueColumn;
			}
		}

		return $result;

	}

	function generateAvgForCandidateQuestionR($candidateIdVar, $candidateNalogVar, $papqIdVar, $pcaIdVar, $papIdVar = 0) {
		Global $db; 

		$result = 0;

		$flagHasGeneralInfo = 0;
		$infoPCA = getInfoEnpalPCAArrayR($candidateIdVar, $pcaIdVar);
		if ($infoPCA["status"] == 1) {
			if ($infoPCA["pca_interviewer"] != "" AND $infoPCA["pca_recommendation"] != 0 AND $infoPCA["pca_reason_recommendation"] != "") {
				$flagHasGeneralInfo = 1;
			}
		}
		unset($infoPCA);

		if ($papIdVar == 0) {
			$appointmentId = getAppointmentForQuestion($papqIdVar);
		} else {
			$appointmentId = $papIdVar;
		}
		
		if($appointmentId != 0){
			$appointmentQuestionForAppointment = array();
			$appointmentQuestionForAppointment = getAppointmentQuestionForAppointmentEnpalArrayR($appointmentId);
			if(count($appointmentQuestionForAppointment["count"]) != 0){

				$hasRatingSum = 0;
				$hasOptionSum = 0;
				$hasTextSum = 0;

				$hasValueRatingSum = 0;
				$hasValueOptionSum = 0;
				$hasValueTextSum = 0;

				$sumRatingValue = 0;
				$avg = 0;

				foreach($appointmentQuestionForAppointment["count"] AS $countAppQuestions){
					$checkValueRating = array();
					if ($appointmentQuestionForAppointment["pqu_has_rating"][$countAppQuestions] == 1){
						$hasRatingSum = $hasRatingSum + 1; 
						$checkValueRating = valueCheckForCandidateQuestion($candidateIdVar, $appointmentQuestionForAppointment["papq_id"][$countAppQuestions], "pra_rating");
						if ($checkValueRating["status"] == 1) {
							$hasValueRatingSum = $hasValueRatingSum + 1;
							$sumRatingValue = $sumRatingValue + $checkValueRating["value"];
						}
					}
					unset($checkValueRating);
					$checkValueOption = array();
					if ($appointmentQuestionForAppointment["pqu_has_dropdown"][$countAppQuestions] == 1){
						$hasOptionSum = $hasOptionSum + 1;
						$checkValueOption = valueCheckForCandidateQuestion($candidateIdVar, $appointmentQuestionForAppointment["papq_id"][$countAppQuestions], "pra_options_value");
						if ($checkValueOption["status"] == 1) {
							$hasValueOptionSum = $hasValueOptionSum + 1;
						}
					}
					unset($checkValueOption); 
					$checkValueText = array();
					if ($appointmentQuestionForAppointment["pqu_has_text"][$countAppQuestions] == 1 AND $appointmentQuestionForAppointment["pqu_has_dropdown"][$countAppQuestions] == 0 AND $appointmentQuestionForAppointment["pqu_has_rating"][$countAppQuestions] == 0){
						$hasTextSum = $hasTextSum + 1;
						$checkValueText = valueCheckForCandidateQuestion($candidateIdVar, $appointmentQuestionForAppointment["papq_id"][$countAppQuestions], "pra_comment");
						if ($checkValueText["status"] == 1) {
							$hasValueTextSum = $hasValueTextSum + 1;
						}
					}
					unset($checkValueText); 
				}

				$flagHasAllRating = 0; 
				$flagHasAllOption = 0;
				$flagHasAllText = 0;

				if ($hasRatingSum == $hasValueRatingSum) {
					$flagHasAllRating = 1;
				}

				if ($hasOptionSum == $hasValueOptionSum) {
					$flagHasAllOption = 1;
				}

				if ($hasTextSum == $hasValueTextSum) {
					$flagHasAllText = 1;
				}

				if($flagHasGeneralInfo == 1 AND $flagHasAllRating == 1 AND $flagHasAllOption == 1 AND $flagHasAllText == 1){
					$avg = round($sumRatingValue/$hasRatingSum, 0, PHP_ROUND_HALF_UP);
					$queryUpdateAvg = $db->prepare("
						UPDATE
							idk_pp_cand_appts
						SET
							pca_avg_rating = :pca_avg_rating
						WHERE 
							pca_id = :pca_id
					");
					$queryUpdateAvg->execute(array(
						':pca_id' => $pcaIdVar,
						':pca_avg_rating' => $avg
					));
					$result = 1;
				}

				if (($hasValueRatingSum == 1 AND $hasValueOptionSum == 0 AND $hasValueTextSum == 0) OR ($hasValueRatingSum == 0 AND $hasValueOptionSum == 1 AND $hasValueTextSum == 0) OR ($hasValueRatingSum == 0 AND $hasValueOptionSum == 0 AND $hasValueTextSum == 1)) {
					/*
						Unos u TF START
						*/
							/* 
								Prilikom unosa prve ocjene kod kandidata potvrđuje se dolazak kandidata na Intervju. 
								Zbog toga se automatski unosi TF bilješka i ostale radnje prikazane dalje u kôdu. 
							*/

							/* DEFINISANJE PORUKE ZA BILJESKU START */
								$note = "Automatska bilješka prilikom unosa prve ocjene na Intervju. Prvom ocjenom potvrđen je dolazak kandidata na Intervju.";
							/* DEFINISANJE PORUKE ZA BILJESKU END */

							/* UZIMANJE ID PROJEKTA ZA NALOG U KOJEM SE KANDIDAT NALAZI START */
								$queryIntervjuProject = $db->prepare("
									SELECT 
										pro.project_id
									FROM 
										idk_kandidati kan
									JOIN 
										idk_project_kandidati pk
									ON 
										pk.pk_kandidatid = kan.kandidat_id
									JOIN 
										idk_projects pro
									ON 
										pro.project_id = pk.pk_projectid
									WHERE 
										pro.project_nalogid = :project_nalogid
									AND 
										kan.kandidat_id = :kandidat_id 
									AND 
										kan.kandidat_status != 3
									AND 
										pro.project_name LIKE ('%Intervju%')
								");
								$queryIntervjuProject->execute(array(
									':project_nalogid' => $candidateNalogVar, 
									':kandidat_id' => $candidateIdVar
								));
								if($queryIntervjuProject->rowCount() == 1){
									$rowIntervjuProject = $queryIntervjuProject->fetch();
									$projectId = intval($rowIntervjuProject["project_id"]);
								}else{
									$projectId = 0;
								}
							/* UZIMANJE ID PROJEKTA ZA NALOG U KOJEM SE KANDIDAT NALAZI END */
							
							/* UZIMANJE ID GRUPE CASTINGA PREKO TERMINA NA KOJEM SE KANDIDAT OCJENJUJE START */
								$queryGroupIntervju = $db->prepare("
									SELECT 
										pap_group_id
									FROM 
										idk_pp_appointments
									WHERE 
										pap_id = :pap_id
								");
								$queryGroupIntervju->execute(array(
									':pap_id' => $appointmentId
								));
								if($queryGroupIntervju->rowCount() == 1){
									$rowGroupIntervju = $queryGroupIntervju->fetch();
									$castingId = intval($rowGroupIntervju["pap_group_id"]);
								}else{
									$castingId = 0;
								}
							/* UZIMANJE ID GRUPE CASTINGA PREKO TERMINA NA KOJEM SE KANDIDAT OCJENJUJE END */

							/* AKO JE KANDIDAT VEĆ U TF_STATS_RESERVATIONS ZA OVAJ CASTING, TJ OZNAČEN JE RUČNO DA JE DOŠAO, ONDA NE TREBA OVDJE RADITI UPDATE START */
								$query_check_tf_stats = $db->prepare("
									SELECT tsr_id 
									FROM idk_tf_stats_reservations 
									WHERE tsr_candidate_id = :tsr_candidate_id AND tsr_interview_id = :tsr_interview_id AND tsr_status = 1
								");
								$query_check_tf_stats->execute(array(
									':tsr_candidate_id' => $candidateIdVar,
									':tsr_interview_id' => $castingId
								));
								if($query_check_tf_stats->rowCount() == 0){
									
									/* UPDATE ZADNJE AKTIVNE FB BILJESKE KOD KANDIDATA START */
										updateLastActiveTaskForCandidate($candidateIdVar, 1);
									/* UPDATE ZADNJE AKTIVNE FB BILJESKE KOD KANDIDATA END */
	
									/* RESET TF STATUSA U GLAVNOJ TABELI KANDIDATA START */
										resetTaskForceStatusForCandidate($candidateIdVar);
									/* RESET TF STATUSA U GLAVNOJ TABELI KANDIDATA END */
	
									/* UNOS TASK FORCE BILJESKE NA KRAJU START */
										$queryInsertTF = $db->prepare("
											INSERT INTO idk_task_force
												(
													tf_candidate_id, 
													tf_nalog_id,
													tf_project_id,
													tf_casting_id, 
													tf_status_id, 
													tf_note,
													tf_important_note, 
													tf_last_active_task
												)
											VALUES
												(
													:tf_candidate_id, 
													:tf_nalog_id,
													:tf_project_id,
													:tf_casting_id, 
													:tf_status_id, 
													:tf_note,
													:tf_important_note, 
													:tf_last_active_task
												)
										");
										$queryInsertTF->execute(array(
											':tf_candidate_id' => $candidateIdVar,
											':tf_nalog_id' => $candidateNalogVar,
											':tf_project_id' => $projectId,
											':tf_casting_id' => $castingId, 
											'tf_status_id' => 20, 
											'tf_note' => $note,
											'tf_important_note' => 0,
											'tf_last_active_task' => 0
										));
									/* UNOS TASK FORCE BILJESKE NA KRAJU END */
	
									/* UNOS TASK FORCE STATISTIKA START */
										$last_agent_id = disconnectAgentFromCandidate($candidateIdVar);
										
										$active_tsr_id = checkTFstatsPristaoDolaziForCandidate($candidateIdVar, $castingId);
										if($active_tsr_id != null){
											updateTFStat($active_tsr_id, 1);
										}
									/* UNOS TASK FORCE STATISTIKA START */
	
									/* ODVEZIVANJE AGENTA I KANDIDATA START */
										$query_disconnect_agent = $db->prepare("
											UPDATE idk_kandidati
											SET tf_reserved_agent = :tf_reserved_agent
											WHERE kandidat_id = :kandidat_id
										");
		
										$query_disconnect_agent->execute(array(
											':tf_reserved_agent' => NULL,
											':kandidat_id' => $candidateIdVar
										));
									/* ODVEZIVANJE AGENTA I KANDIDATA START */
								}
							/* AKO JE KANDIDAT VEĆ U TF_STATS_RESERVATIONS ZA OVAJ CASTING, TJ OZNAČEN JE RUČNO DA JE DOŠAO, ONDA NE TREBA OVDJE RADITI UPDATE END */
						/*
						Unos u TF START
					*/
				}

			}
			unset($appointmentQuestionForAppointment);
		}

		return $result;
	}

	function generateAvgForCandidateQuestionNewR($candidateIdVar, $candidateNalogVar, $papqIdVar, $pcaIdVar, $papIdVar = 0) {
		Global $db; 

		$result = 0;

		if ($papIdVar == 0) {
			$appointmentId = getAppointmentForQuestion($papqIdVar);
		} else {
			$appointmentId = $papIdVar;
		}
		
		if($appointmentId != 0){
			$appointmentQuestionForAppointment = array();
			$appointmentQuestionForAppointment = getAppointmentQuestionForAppointmentArrayR($appointmentId);
			if(count($appointmentQuestionForAppointment["count"]) != 0){

				$hasRatingSum = 0;
				$hasOptionSum = 0;
				$hasTextSum = 0;

				$hasValueRatingSum = 0;
				$hasValueOptionSum = 0;
				$hasValueTextSum = 0;

				$sumRatingValue = 0;
				$avg = 0;

				foreach($appointmentQuestionForAppointment["count"] AS $countAppQuestions){
					$checkValueRating = array();
					if ($appointmentQuestionForAppointment["pqu_has_rating"][$countAppQuestions] == 1){
						$hasRatingSum = $hasRatingSum + 1; 
						$checkValueRating = valueCheckForCandidateQuestion($candidateIdVar, $appointmentQuestionForAppointment["papq_id"][$countAppQuestions], "pra_rating");
						if ($checkValueRating["status"] == 1) {
							$hasValueRatingSum = $hasValueRatingSum + 1;
							$sumRatingValue = $sumRatingValue + $checkValueRating["value"];
						}
					}
					unset($checkValueRating);
					$checkValueOption = array();
					if ($appointmentQuestionForAppointment["pqu_has_dropdown"][$countAppQuestions] == 1){
						$hasOptionSum = $hasOptionSum + 1;
						$checkValueOption = valueCheckForCandidateQuestion($candidateIdVar, $appointmentQuestionForAppointment["papq_id"][$countAppQuestions], "pra_options_value");
						if ($checkValueOption["status"] == 1) {
							$hasValueOptionSum = $hasValueOptionSum + 1;
						}
					}
					unset($checkValueOption); 
					$checkValueText = array();
					if ($appointmentQuestionForAppointment["pqu_has_text"][$countAppQuestions] == 1 AND $appointmentQuestionForAppointment["pqu_has_dropdown"][$countAppQuestions] == 0 AND $appointmentQuestionForAppointment["pqu_has_rating"][$countAppQuestions] == 0){
						$hasTextSum = $hasTextSum + 1;
						$checkValueText = valueCheckForCandidateQuestion($candidateIdVar, $appointmentQuestionForAppointment["papq_id"][$countAppQuestions], "pra_comment");
						if ($checkValueText["status"] == 1) {
							$hasValueTextSum = $hasValueTextSum + 1;
						}
					}
					unset($checkValueText); 
				}

				$flagHasAllRating = 0; 
				$flagHasAllOption = 0;
				$flagHasAllText = 0;

				if ($hasRatingSum == $hasValueRatingSum) {
					$flagHasAllRating = 1;
				}

				if ($hasOptionSum == $hasValueOptionSum) {
					$flagHasAllOption = 1;
				}

				if ($hasTextSum == $hasValueTextSum) {
					$flagHasAllText = 1;
				}

				if($flagHasAllRating == 1 AND $flagHasAllOption == 1 AND $flagHasAllText == 1){
					$avg = round($sumRatingValue/$hasRatingSum, 0, PHP_ROUND_HALF_UP);
					$queryUpdateAvg = $db->prepare("
						UPDATE
							idk_pp_cand_appts
						SET
							pca_avg_rating = :pca_avg_rating
						WHERE 
							pca_id = :pca_id
					");
					$queryUpdateAvg->execute(array(
						':pca_id' => $pcaIdVar,
						':pca_avg_rating' => $avg
					));
					$result = 1;
				}
				
				if (($hasValueRatingSum == 1 AND $hasValueOptionSum == 0 AND $hasValueTextSum == 0) OR ($hasValueRatingSum == 0 AND $hasValueOptionSum == 1 AND $hasValueTextSum == 0) OR ($hasValueRatingSum == 0 AND $hasValueOptionSum == 0 AND $hasValueTextSum == 1)) {
					/*
						Unos u TF START
						*/
							/* 
								Prilikom unosa prve ocjene kod kandidata potvrđuje se dolazak kandidata na Intervju. 
								Zbog toga se automatski unosi TF bilješka i ostale radnje prikazane dalje u kôdu. 
							*/

							/* DEFINISANJE PORUKE ZA BILJESKU START */
								$note = "Automatska bilješka prilikom unosa prve ocjene na Intervju. Prvom ocjenom potvrđen je dolazak kandidata na Intervju.";
							/* DEFINISANJE PORUKE ZA BILJESKU END */

							/* UZIMANJE ID PROJEKTA ZA NALOG U KOJEM SE KANDIDAT NALAZI START */
								$queryIntervjuProject = $db->prepare("
									SELECT 
										pro.project_id
									FROM 
										idk_kandidati kan
									JOIN 
										idk_project_kandidati pk
									ON 
										pk.pk_kandidatid = kan.kandidat_id
									JOIN 
										idk_projects pro
									ON 
										pro.project_id = pk.pk_projectid
									WHERE 
										pro.project_nalogid = :project_nalogid
									AND 
										kan.kandidat_id = :kandidat_id 
									AND 
										kan.kandidat_status != 3
									AND 
										pro.project_name LIKE ('%Intervju%')
								");
								$queryIntervjuProject->execute(array(
									':project_nalogid' => $candidateNalogVar, 
									':kandidat_id' => $candidateIdVar
								));
								if($queryIntervjuProject->rowCount() == 1){
									$rowIntervjuProject = $queryIntervjuProject->fetch();
									$projectId = intval($rowIntervjuProject["project_id"]);
								}else{
									$projectId = 0;
								}
							/* UZIMANJE ID PROJEKTA ZA NALOG U KOJEM SE KANDIDAT NALAZI END */
							
							/* UZIMANJE ID GRUPE CASTINGA PREKO TERMINA NA KOJEM SE KANDIDAT OCJENJUJE START */
								$queryGroupIntervju = $db->prepare("
									SELECT 
										pap_group_id
									FROM 
										idk_pp_appointments
									WHERE 
										pap_id = :pap_id
								");
								$queryGroupIntervju->execute(array(
									':pap_id' => $appointmentId
								));
								if($queryGroupIntervju->rowCount() == 1){
									$rowGroupIntervju = $queryGroupIntervju->fetch();
									$castingId = intval($rowGroupIntervju["pap_group_id"]);
								}else{
									$castingId = 0;
								}
							/* UZIMANJE ID GRUPE CASTINGA PREKO TERMINA NA KOJEM SE KANDIDAT OCJENJUJE END */

							/* AKO JE KANDIDAT VEĆ U TF_STATS_RESERVATIONS ZA OVAJ CASTING, TJ OZNAČEN JE RUČNO DA JE DOŠAO, ONDA NE TREBA OVDJE RADITI UPDATE START */
								$query_check_tf_stats = $db->prepare("
									SELECT tsr_id 
									FROM idk_tf_stats_reservations 
									WHERE tsr_candidate_id = :tsr_candidate_id AND tsr_interview_id = :tsr_interview_id AND tsr_status = 1
								");
								$query_check_tf_stats->execute(array(
									':tsr_candidate_id' => $candidateIdVar,
									':tsr_interview_id' => $castingId
								));
								if($query_check_tf_stats->rowCount() == 0){
									
									/* UPDATE ZADNJE AKTIVNE FB BILJESKE KOD KANDIDATA START */
										updateLastActiveTaskForCandidate($candidateIdVar, 1);
									/* UPDATE ZADNJE AKTIVNE FB BILJESKE KOD KANDIDATA END */
	
									/* RESET TF STATUSA U GLAVNOJ TABELI KANDIDATA START */
										resetTaskForceStatusForCandidate($candidateIdVar);
									/* RESET TF STATUSA U GLAVNOJ TABELI KANDIDATA END */
	
									/* UNOS TASK FORCE BILJESKE NA KRAJU START */
										$queryInsertTF = $db->prepare("
											INSERT INTO idk_task_force
												(
													tf_candidate_id, 
													tf_nalog_id,
													tf_project_id,
													tf_casting_id, 
													tf_status_id, 
													tf_note,
													tf_important_note, 
													tf_last_active_task
												)
											VALUES
												(
													:tf_candidate_id, 
													:tf_nalog_id,
													:tf_project_id,
													:tf_casting_id, 
													:tf_status_id, 
													:tf_note,
													:tf_important_note, 
													:tf_last_active_task
												)
										");
										$queryInsertTF->execute(array(
											':tf_candidate_id' => $candidateIdVar,
											':tf_nalog_id' => $candidateNalogVar,
											':tf_project_id' => $projectId,
											':tf_casting_id' => $castingId, 
											'tf_status_id' => 20, 
											'tf_note' => $note,
											'tf_important_note' => 0,
											'tf_last_active_task' => 0
										));
									/* UNOS TASK FORCE BILJESKE NA KRAJU END */
	
									/* UNOS TASK FORCE STATISTIKA START */
										$last_agent_id = disconnectAgentFromCandidate($candidateIdVar);
										
										$active_tsr_id = checkTFstatsPristaoDolaziForCandidate($candidateIdVar, $castingId);
										if($active_tsr_id != null){
											updateTFStat($active_tsr_id, 1);
										}
									/* UNOS TASK FORCE STATISTIKA START */
	
									/* ODVEZIVANJE AGENTA I KANDIDATA START */
										$query_disconnect_agent = $db->prepare("
											UPDATE idk_kandidati
											SET tf_reserved_agent = :tf_reserved_agent
											WHERE kandidat_id = :kandidat_id
										");
		
										$query_disconnect_agent->execute(array(
											':tf_reserved_agent' => NULL,
											':kandidat_id' => $candidateIdVar
										));
									/* ODVEZIVANJE AGENTA I KANDIDATA START */
								}
							/* AKO JE KANDIDAT VEĆ U TF_STATS_RESERVATIONS ZA OVAJ CASTING, TJ OZNAČEN JE RUČNO DA JE DOŠAO, ONDA NE TREBA OVDJE RADITI UPDATE END */
						/*
						Unos u TF START
					*/
				}

			}
			unset($appointmentQuestionForAppointment);
		}

		return $result;
	}
	
	function valueInsertUpdateForCandidateQuestionArrayR($candidateIdVar, $candidateNalogVar, $papqIdVar, $pcaIdVar, $columnNameVar, $valueVar){
		
		Global $db; 

		$candidateIdVar = intval($candidateIdVar);
		$candidateNalogVar = intval($candidateNalogVar);
		$papqIdVar = intval($papqIdVar);
		$pcaIdVar = intval($pcaIdVar);
		$columnNameVar = $columnNameVar;
		$flagValue = 0;
		if ($columnNameVar == "pra_rating") {
			$valueVar = intval($valueVar);
			$flagValue = (($valueVar != 0) ? 1 : 0);
		} else if ($columnNameVar == "pra_options_value") {
			$valueVar = intval($valueVar);
			$flagValue = (($valueVar != 0) ? 1 : 0);
		} else {
			$valueVar = $valueVar;
			$flagValue = (($valueVar != "") ? 1 : 0);
		}

		$result = array(
			"status" => 102, 
			"avgUpdate" => 0
		);

		if ($candidateIdVar != 0 AND $candidateNalogVar != 0 AND $papqIdVar != 0 AND $pcaIdVar != 0 AND $columnNameVar != "" AND $flagValue != 0) {
			
			$checkRatingData = valueCheckForCandidateQuestion($candidateIdVar, $papqIdVar, "pra_rating");
			$checkOptionData = valueCheckForCandidateQuestion($candidateIdVar, $papqIdVar, "pra_options_value");
			$checkCommentData = valueCheckForCandidateQuestion($candidateIdVar, $papqIdVar, "pra_comment");
			
			if ($checkRatingData["status"] == 101 AND $checkOptionData["status"] == 101 AND $checkCommentData["status"] == 101) {
				$queryInsert = $db->prepare("
					INSERT INTO idk_pp_ratings
						(
							".$columnNameVar.", 
							pra_appointment_question_id, 
							pra_kandidat_id
						)
					VALUES
						(
							:valueVar, 
							:pra_appointment_question_id, 
							:pra_kandidat_id
						)
				");
				$queryInsert->execute(array(
					':valueVar' => $valueVar,
					':pra_appointment_question_id' => $papqIdVar,
					':pra_kandidat_id' => $candidateIdVar
				));

				$result["status"] = 100;
			} else {
				$queryUpdate = $db->prepare("
					UPDATE
						idk_pp_ratings
					SET 
						".$columnNameVar." = :valueVar
					WHERE 
						pra_appointment_question_id = :pra_appointment_question_id
						AND
						pra_kandidat_id = :pra_kandidat_id
						AND 
						pra_id = (
							SELECT 
								MAX(ppr.pra_id)
							FROM (
								SELECT 
									ppr1.pra_id
								FROM 
									idk_pp_ratings ppr1
								WHERE 
									ppr1.pra_kandidat_id = :pra_kandidat_id
									AND 
									ppr1.pra_appointment_question_id = :pra_appointment_question_id
							) AS ppr
						)
				");
				$queryUpdate->execute(array(
					':valueVar' => $valueVar,
					':pra_appointment_question_id' => $papqIdVar,
					':pra_kandidat_id' => $candidateIdVar
				));

				$result["status"] = 101;
			}

			unset($checkRatingData);
			unset($checkOptionData);
			unset($checkCommentData);

			$result["avgUpdate"] = generateAvgForCandidateQuestionR($candidateIdVar, $candidateNalogVar, $papqIdVar, $pcaIdVar);

		}

		return $result;

	}

	function valueInsertUpdateForCandidateQuestionNewArrayR($candidateIdVar, $candidateNalogVar, $papqIdVar, $pcaIdVar, $columnNameVar, $valueVar){
		
		Global $db; 

		$candidateIdVar = intval($candidateIdVar);
		$candidateNalogVar = intval($candidateNalogVar);
		$papqIdVar = intval($papqIdVar);
		$pcaIdVar = intval($pcaIdVar);
		$columnNameVar = $columnNameVar;
		$flagValue = 0;
		if ($columnNameVar == "pra_rating") {
			$valueVar = intval($valueVar);
			$flagValue = (($valueVar != 0) ? 1 : 0);
		} else if ($columnNameVar == "pra_options_value") {
			$valueVar = intval($valueVar);
			$flagValue = (($valueVar != 0) ? 1 : 0);
		} else {
			$valueVar = $valueVar;
			$flagValue = (($valueVar != "") ? 1 : 0);
		}

		$result = array(
			"status" => 102, 
			"avgUpdate" => 0
		);

		if ($candidateIdVar != 0 AND $candidateNalogVar != 0 AND $papqIdVar != 0 AND $pcaIdVar != 0 AND $columnNameVar != "" AND $flagValue != 0) {
			
			$checkRatingData = valueCheckForCandidateQuestion($candidateIdVar, $papqIdVar, "pra_rating");
			$checkOptionData = valueCheckForCandidateQuestion($candidateIdVar, $papqIdVar, "pra_options_value");
			$checkCommentData = valueCheckForCandidateQuestion($candidateIdVar, $papqIdVar, "pra_comment");
			
			if ($checkRatingData["status"] == 101 AND $checkOptionData["status"] == 101 AND $checkCommentData["status"] == 101) {
				$queryInsert = $db->prepare("
					INSERT INTO idk_pp_ratings
						(
							".$columnNameVar.", 
							pra_appointment_question_id, 
							pra_kandidat_id
						)
					VALUES
						(
							:valueVar, 
							:pra_appointment_question_id, 
							:pra_kandidat_id
						)
				");
				$queryInsert->execute(array(
					':valueVar' => $valueVar,
					':pra_appointment_question_id' => $papqIdVar,
					':pra_kandidat_id' => $candidateIdVar
				));

				$result["status"] = 100;
			} else {
				$queryUpdate = $db->prepare("
					UPDATE
						idk_pp_ratings
					SET 
						".$columnNameVar." = :valueVar
					WHERE 
						pra_appointment_question_id = :pra_appointment_question_id
						AND
						pra_kandidat_id = :pra_kandidat_id
						AND 
						pra_id = (
							SELECT 
								MAX(ppr.pra_id)
							FROM (
								SELECT 
									ppr1.pra_id
								FROM 
									idk_pp_ratings ppr1
								WHERE 
									ppr1.pra_kandidat_id = :pra_kandidat_id
									AND 
									ppr1.pra_appointment_question_id = :pra_appointment_question_id
							) AS ppr
						)
				");
				$queryUpdate->execute(array(
					':valueVar' => $valueVar,
					':pra_appointment_question_id' => $papqIdVar,
					':pra_kandidat_id' => $candidateIdVar
				));

				$result["status"] = 101;
			}

			unset($checkRatingData);
			unset($checkOptionData);
			unset($checkCommentData);

			$result["avgUpdate"] = generateAvgForCandidateQuestionNewR($candidateIdVar, $candidateNalogVar, $papqIdVar, $pcaIdVar);

		}

		return $result;

	}

	function ratingInsertUpdate($canId, $papq_id, $pca_id, $value_rating){
		Global $db;
		$canId = intval($canId);
		$papq_id = intval($papq_id);
		$pca_id = intval($pca_id);
		$value_rating = intval($value_rating);
		$functionResults = array(
			"avgUpdate" => array(),
			"action" => array(), 
		);
		if($canId != 0 AND $papq_id != 0 AND $pca_id != 0 AND $value_rating != 0){
			$checkRatingData = getRatingForAppointmentQuestion($canId, $papq_id);
			$checkComentData = getCommentForAppointmentQuestion($canId, $papq_id);
			
			if($checkRatingData != 102 AND $checkComentData != 102){
				if($checkRatingData == 101 AND $checkComentData == 101){
					//radi insert
					$queryInsert = $db->prepare("
						INSERT INTO idk_pp_ratings
							(
								pra_rating, 
								pra_appointment_question_id, 
								pra_kandidat_id
							)
						VALUES
							(
								:pra_rating, 
								:pra_appointment_question_id, 
								:pra_kandidat_id
							)
					");
					$queryInsert->execute(array(
						':pra_rating' => $value_rating,
						':pra_appointment_question_id' => $papq_id,
						':pra_kandidat_id' => $canId
					));
					array_push($functionResults["action"], 1);
				}else{
					//radi update
					$queryUpdate = $db->prepare("
						UPDATE
							idk_pp_ratings
						SET 
							pra_rating = :pra_rating
						WHERE 
							pra_appointment_question_id = :pra_appointment_question_id
							AND
							pra_kandidat_id = :pra_kandidat_id
							AND 
							pra_id = (
								SELECT 
									MAX(ppr.pra_id)
								FROM (
									SELECT 
										ppr1.pra_id
									FROM 
										idk_pp_ratings ppr1
									WHERE 
										ppr1.pra_kandidat_id = :pra_kandidat_id
										AND 
										ppr1.pra_appointment_question_id = :pra_appointment_question_id
								) AS ppr
							)
					");
					$queryUpdate->execute(array(
						':pra_rating' => $value_rating,
						':pra_appointment_question_id' => $papq_id,
						':pra_kandidat_id' => $canId
					));
					array_push($functionResults["action"], 2);
				}
				$appointmentId = getAppointmentForQuestion($papq_id);
				if($appointmentId != 0){
					$appointmentQuestionForAppointment = array();
					$appointmentQuestionForAppointment = getAppointmentQuestionForAppointmentArrayR($appointmentId);
					if(count($appointmentQuestionForAppointment["count"]) != 0){
						$imaUkupno = count($appointmentQuestionForAppointment["count"]);
						$brojac = 0;
						$suma = 0;
						foreach($appointmentQuestionForAppointment["count"] AS $countAppQuestions){
							$rating = getRatingForAppointmentQuestion($canId, $appointmentQuestionForAppointment["papq_id"][$countAppQuestions]);
							if($rating != 100 AND $rating != 101 AND $rating != 102){
								$brojac++;
								$suma = $suma + $rating;
							}
						}

						if($brojac == 1){
							/* 
								Prilikom unosa prve ocjene kod kandidata potvrđuje se dolazak kandidata na Intervju. 
								Zbog toga se automatski unosi TF bilješka i ostale radnje prikazane dalje u kôdu. 
							*/
								/* DEFINISANJE PORUKE ZA BILJESKU START */
								$note = "Automatska bilješka prilikom unosa prve ocjene na Intervju. Prvom ocjenom potvrđen je dolazak kandidata na Intervju.";
								/* DEFINISANJE PORUKE ZA BILJESKU END */

								/* UZIMANJE ID NALOGA ZA KANDIDATA START */
									$nalogId = getNalogForCandidatR($canId); //Uzimanje naloga za kandidata
								/* UZIMANJE ID NALOGA ZA KANDIDATA END */

								/* UZIMANJE ID PROJEKTA ZA NALOG U KOJEM SE KANDIDAT NALAZI START */
									$queryIntervjuProject = $db->prepare("
										SELECT 
											pro.project_id
										FROM 
											idk_kandidati kan
										JOIN 
											idk_project_kandidati pk
										ON 
											pk.pk_kandidatid = kan.kandidat_id
										JOIN 
											idk_projects pro
										ON 
											pro.project_id = pk.pk_projectid
										WHERE 
											pro.project_nalogid = :project_nalogid
										AND 
											kan.kandidat_id = :kandidat_id 
										AND 
											kan.kandidat_status != 3
										AND 
											pro.project_name LIKE ('%Intervju%')
									");
									$queryIntervjuProject->execute(array(
										':project_nalogid' => $nalogId, 
										':kandidat_id' => $canId
									));
									if($queryIntervjuProject->rowCount() == 1){
										$rowIntervjuProject = $queryIntervjuProject->fetch();
										$projectId = intval($rowIntervjuProject["project_id"]);
									}else{
										$projectId = 0;
									}
								/* UZIMANJE ID PROJEKTA ZA NALOG U KOJEM SE KANDIDAT NALAZI END */
								
								/* UZIMANJE ID GRUPE CASTINGA PREKO TERMINA NA KOJEM SE KANDIDAT OCJENJUJE START */
									$queryGroupIntervju = $db->prepare("
										SELECT 
											pap_group_id
										FROM 
											idk_pp_appointments
										WHERE 
											pap_id = :pap_id
									");
									$queryGroupIntervju->execute(array(
										':pap_id' => $appointmentId
									));
									if($queryGroupIntervju->rowCount() == 1){
										$rowGroupIntervju = $queryGroupIntervju->fetch();
										$castingId = intval($rowGroupIntervju["pap_group_id"]);
									}else{
										$castingId = 0;
									}

								//AKO JE KANDIDAT VEĆ U TF_STATS_RESERVATIONS ZA OVAJ CASTING, TJ OZNAČEN JE RUČNO DA JE DOŠAO, 
								//ONDA NE TREBA OVDJE RADITI UPDATE
								$query_check_tf_stats = $db->prepare("
									SELECT tsr_id 
									FROM idk_tf_stats_reservations 
									WHERE tsr_candidate_id = :tsr_candidate_id AND tsr_interview_id = :tsr_interview_id AND tsr_status = 1
								");
								$query_check_tf_stats->execute(array(
									':tsr_candidate_id' => $canId,
									':tsr_interview_id' => $castingId
								));
								if($query_check_tf_stats->rowCount() == 0){

									/* UZIMANJE ID GRUPE CASTINGA PREKO TERMINA NA KOJEM SE KANDIDAT OCJENJUJE END */
									
									/* UPDATE ZADNJE AKTIVNE FB BILJESKE KOD KANDIDATA START */
										updateLastActiveTaskForCandidate($canId, 1);
									/* UPDATE ZADNJE AKTIVNE FB BILJESKE KOD KANDIDATA END */
	
									/* RESET TF STATUSA U GLAVNOJ TABELI KANDIDATA START */
										resetTaskForceStatusForCandidate($canId);
									/* RESET TF STATUSA U GLAVNOJ TABELI KANDIDATA END */
	
									/* UNOS TASK FORCE BILJESKE NA KRAJU START */
										$queryInsertTF = $db->prepare("
											INSERT INTO idk_task_force
												(
													tf_candidate_id, 
													tf_nalog_id,
													tf_project_id,
													tf_casting_id, 
													tf_status_id, 
													tf_note,
													tf_important_note, 
													tf_last_active_task
												)
											VALUES
												(
													:tf_candidate_id, 
													:tf_nalog_id,
													:tf_project_id,
													:tf_casting_id, 
													:tf_status_id, 
													:tf_note,
													:tf_important_note, 
													:tf_last_active_task
												)
										");
										$queryInsertTF->execute(array(
											':tf_candidate_id' => $canId,
											':tf_nalog_id' => $nalogId,
											':tf_project_id' => $projectId,
											':tf_casting_id' => $castingId, 
											'tf_status_id' => 20, 
											'tf_note' => $note,
											'tf_important_note' => 0,
											'tf_last_active_task' => 0
										));
									/* UNOS TASK FORCE BILJESKE NA KRAJU START */
	
									/* UNOS TASK FORCE STATISTIKA */
									$last_agent_id = disconnectAgentFromCandidate($canId);
									
									$active_tsr_id = checkTFstatsPristaoDolaziForCandidate($canId, $castingId);
									if($active_tsr_id != null){
										updateTFStat($active_tsr_id, 1);
									}

									// if($last_agent_id != null)
									// 	insertTFStat($canId, $last_agent_id, $castingId, $nalogId, 1);
	
									/* ODVEZIVANJE AGENTA I KANDIDATA */
	
									$query_disconnect_agent = $db->prepare("
										UPDATE idk_kandidati
										SET tf_reserved_agent = :tf_reserved_agent
										WHERE kandidat_id = :kandidat_id
									");
	
									$query_disconnect_agent->execute(array(
										':tf_reserved_agent' => NULL,
										':kandidat_id' => $canId
									));
								}
						}
						
						if($imaUkupno == $brojac){
							$avg = round($suma/$imaUkupno, 0, PHP_ROUND_HALF_UP);
							$queryUpdateAvg = $db->prepare("
								UPDATE
									idk_pp_cand_appts
								SET
									pca_avg_rating = :pca_avg_rating
								WHERE 
									pca_id = :pca_id
							");
							$queryUpdateAvg->execute(array(
								':pca_id' => $pca_id,
								':pca_avg_rating' => $avg
							));
							array_push($functionResults["avgUpdate"], 1);
						}else{
							array_push($functionResults["avgUpdate"], 0);
						}
					}else{
						array_push($functionResults["avgUpdate"], 0);
					}
					
					unset($appointmentQuestionForAppointment);
				}else{
					array_push($functionResults["avgUpdate"], 0);
				}
			}else{
				array_push($functionResults["avgUpdate"], 0);
				array_push($functionResults["action"], 0);
			}
			return $functionResults;
		}else{
			array_push($functionResults["avgUpdate"], 0);
			array_push($functionResults["action"], 0);
			return $functionResults;
		}
		unset($functionResults);
	}

	function disconnectAgentFromCandidate($kandidat_id){
		Global $db;
	
		$query_get_agent = $db->prepare("
			SELECT tf_reserved_agent 
			FROM idk_kandidati
			WHERE kandidat_id = :kandidat_id
		");
		$query_get_agent->execute(array(
			':kandidat_id' => $kandidat_id
		));
		$row_get_agent = $query_get_agent->fetch();
		$agent_id = $row_get_agent['tf_reserved_agent'];
	
		if($agent_id != null){
	
			$query = $db->prepare("
				UPDATE idk_kandidati
				SET tf_reserved_agent = :tf_reserved_agent
				WHERE kandidat_id = :kandidat_id
			");
	
			$query->execute(array(
				':tf_reserved_agent' => NULL,
				':kandidat_id' => $kandidat_id
			));
	
			return $agent_id;
		}else{
			return null;
		}
	}

	function updateTFStat($tsr_id, $status){
		Global $db;
		$query = $db->prepare("UPDATE idk_tf_stats_reservations SET tsr_status = $status WHERE tsr_id = $tsr_id");
		$query->execute();
	}

	function checkTFstatsPristaoDolaziForCandidate($kandidat_id, $casting_group_id){
		Global $db;
	
		$query_check_tf_stats = $db->prepare("
			SELECT tsr_id FROM idk_tf_stats_reservations
			WHERE tsr_candidate_id = :kandidat_id AND tsr_interview_id = :casting_group_id AND tsr_status IN (4,5)
		");
	
		$query_check_tf_stats->execute(array(
			':kandidat_id' => $kandidat_id,
			':casting_group_id' => $casting_group_id
		));
		$row_tf_stats = $query_check_tf_stats->fetch();
		$tsr_id = $row_tf_stats['tsr_id'];
		
		return $tsr_id;
	}

	function insertTFStat($tsr_candidate_id, $tsr_agent_id, $tsr_interview_id, $tsr_nalog_id, $tsr_status){
		Global $db;
		
		$tsr_link_id = getLinkForCandidate($tsr_candidate_id);
		$query = $db->prepare("
						INSERT INTO idk_tf_stats_reservations
							(tsr_candidate_id, tsr_agent_id, tsr_interview_id, tsr_nalog_id, tsr_link_id, tsr_status)
						VALUES
							(:tsr_candidate_id, :tsr_agent_id, :tsr_interview_id, :tsr_nalog_id, :tsr_link_id, :tsr_status)");
	
		$query->execute(array(
						':tsr_candidate_id' => $tsr_candidate_id,
						':tsr_agent_id' => $tsr_agent_id,
						':tsr_interview_id' => $tsr_interview_id,
						':tsr_nalog_id' => $tsr_nalog_id,
						':tsr_link_id' => $tsr_link_id,
						':tsr_status' => $tsr_status));
	}

	function getLinkForCandidate($kandidat_id){
		Global $db;
	
		$query = $db->prepare("SELECT kandidat_visitedurl FROM idk_kandidati WHERE kandidat_id = $kandidat_id");
		$query->execute();
		$row = $query->fetch();
	
		return $row['kandidat_visitedurl'];
	}
	
	function updateLastActiveTaskForCandidate($kandidat_id, $tf_vrsta_id){

		Global $db;
		$query = $db->prepare("
			UPDATE idk_task_force
			SET tf_last_active_task = :tf_last_active_task
			WHERE tf_candidate_id = :tf_candidate_id AND tf_last_active_task = 1 AND tf_vrsta_id = :tf_vrsta_id
		");
		$query->execute(array(
			':tf_last_active_task' => 0,
			':tf_candidate_id' => $kandidat_id,
			':tf_vrsta_id' => $tf_vrsta_id
		));

	}
	
	function resetTaskForceStatusForCandidate($kandidat_id){
		
		Global $db;
		$query = $db->prepare("
			UPDATE idk_kandidati
			SET kandidat_tf_status = NULL
			WHERE kandidat_id = :kandidat_id 
		");
		$query->execute(array(
			':kandidat_id' => $kandidat_id
		));
	}

	function getRatingForAppointmentQuestion($canId, $papq_id){
		//Funkcija vraca kombinacije
		// 100 - nije ocijenjeno i unesen komentar za to pitanje
		// 101 - nije unesena ocjena i nije unesen komentar za to pitanje
		// 102 - problem sa parametrima koji se daju funkciji
		// vrijednost ocjene 1,2,3,4,5
		Global $db;
		$canId = intval($canId);
		$papq_id = intval($papq_id);
		if($canId != 0 AND $papq_id != 0){
			$query = $db->prepare("
				SELECT 
					pra_rating AS broj
				FROM 
					idk_pp_ratings 
				WHERE 
					pra_appointment_question_id = :pra_appointment_question_id
					AND
					pra_kandidat_id = :pra_kandidat_id
					AND 
					pra_id = (
						SELECT 
							MAX(ppr.pra_id)
						FROM 
							idk_pp_ratings ppr
						WHERE 
							ppr.pra_kandidat_id = :pra_kandidat_id
							AND 
							ppr.pra_appointment_question_id = :pra_appointment_question_id
					)
			");
			$query->execute(array(
				':pra_appointment_question_id' => $papq_id,
				':pra_kandidat_id' => $canId
			));
			
			if($query->rowCount() == 1){
				$row = $query->fetch();
				$broj = intval($row["broj"]);
				if($broj != 0){
					$broj = $broj;
				}else{
					$broj = 100;
				}
			}else{
				$broj = 101;
			}
			return $broj;
		}else{
			return 102;
		}
	}

	function getOptionForAppointmentQuestion($canId, $papq_id){
		//Funkcija vraca kombinacije
		// 100 - nije ocijenjeno i unesen komentar za to pitanje
		// 101 - nije unesena ocjena i nije unesen komentar za to pitanje
		// 102 - problem sa parametrima koji se daju funkciji
		// vrijednost ocjene 1,2,3,4,5
		Global $db;
		$canId = intval($canId);
		$papq_id = intval($papq_id);
		if($canId != 0 AND $papq_id != 0){
			$query = $db->prepare("
				SELECT 
					pra_options_value AS broj
				FROM 
					idk_pp_ratings 
				WHERE 
					pra_appointment_question_id = :pra_appointment_question_id
					AND
					pra_kandidat_id = :pra_kandidat_id
					AND
					pra_id = (
						SELECT 
							MAX(ppr.pra_id)
						FROM 
							idk_pp_ratings ppr
						WHERE 
							ppr.pra_kandidat_id = :pra_kandidat_id
							AND 
							ppr.pra_appointment_question_id = :pra_appointment_question_id
					)
			");
			$query->execute(array(
				':pra_appointment_question_id' => $papq_id,
				':pra_kandidat_id' => $canId
			));
			
			if($query->rowCount() == 1){
				$row = $query->fetch();
				$broj = intval($row["broj"]);
				if($broj != 0){
					$broj = $broj;
				}else{
					$broj = 0;
				}
			}else{
				$broj = 0;
			}
			return $broj;
		}else{
			return 0;
		}
	}
	
	function getAppointmentForQuestion($papq_id){
		Global $db;
		$papq_id = intval($papq_id);
		if($papq_id != 0){
			$query = $db->prepare("
				SELECT 
					papq_appointment_id
				FROM 
					idk_pp_appointments_questions
				WHERE 
					papq_id = :papq_id
			");
			$query->execute(array(
				':papq_id' => $papq_id
			));
			$row = $query->fetch();
			$broj = intval($row["papq_appointment_id"]);
			return $broj;
		}else{
			return 0;
		}
	}
	
	function commentInsertUpdate($canId, $papq_id, $value_comment){
		Global $db; 
		$canId = intval($canId);
		$papq_id = intval($papq_id);
		
		if($canId != 0 AND $papq_id != 0 AND $value_comment != ""){
			$checkRatingData = getRatingForAppointmentQuestion($canId, $papq_id);
			$checkComentData = getCommentForAppointmentQuestion($canId, $papq_id);
			if($checkRatingData != 102 AND $checkComentData != 102){
				if($checkRatingData == 101 AND $checkComentData == 101){
					//radi insert
					$queryInsert = $db->prepare("
						INSERT INTO idk_pp_ratings
							(
								pra_comment, 
								pra_appointment_question_id, 
								pra_kandidat_id
							)
						VALUES
							(
								:pra_comment, 
								:pra_appointment_question_id, 
								:pra_kandidat_id
							)
					");
					$queryInsert->execute(array(
						':pra_comment' => $value_comment,
						':pra_appointment_question_id' => $papq_id,
						':pra_kandidat_id' => $canId
					));
					return 1;
				}else{
					//radi update
					$queryUpdate = $db->prepare("
						UPDATE
							idk_pp_ratings
						SET 
							pra_comment = :pra_comment
						WHERE 
							pra_appointment_question_id = :pra_appointment_question_id
							AND
							pra_kandidat_id = :pra_kandidat_id
							AND 
							pra_id = (
								SELECT 
									MAX(ppr.pra_id)
								FROM (
									SELECT 
										ppr1.pra_id
									FROM 
										idk_pp_ratings ppr1
									WHERE 
										ppr1.pra_kandidat_id = :pra_kandidat_id
										AND 
										ppr1.pra_appointment_question_id = :pra_appointment_question_id
								) AS ppr
							)
					");
					$queryUpdate->execute(array(
						':pra_comment' => $value_comment,
						':pra_appointment_question_id' => $papq_id,
						':pra_kandidat_id' => $canId
					));
					return 2;
				}
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
	
	function getCommentForAppointmentQuestion($canId, $papq_id){
		//Funkcija vraca kombinacije
		// 100 - nije komentarisano i unesena ocjena za to pitanje
		// 101 - nije unesena ocjena i nije unesen komentar za to pitanje
		// 102 - problem sa parametrima koji se daju funkciji
		// vrijednost komentara
		Global $db;
		$canId = intval($canId);
		$papq_id = intval($papq_id);
		if($canId != 0 AND $papq_id != 0){
			$query = $db->prepare("
				SELECT 
					pra_comment AS komentar
				FROM 
					idk_pp_ratings 
				WHERE 
					pra_appointment_question_id = :pra_appointment_question_id
					AND
					pra_kandidat_id = :pra_kandidat_id
					AND
					pra_id = (
						SELECT 
							MAX(ppr.pra_id)
						FROM 
							idk_pp_ratings ppr
						WHERE 
							ppr.pra_kandidat_id = :pra_kandidat_id
							AND 
							ppr.pra_appointment_question_id = :pra_appointment_question_id
					)
			");
			$query->execute(array(
				':pra_appointment_question_id' => $papq_id,
				':pra_kandidat_id' => $canId
			));
			
			if($query->rowCount() == 1){
				$row = $query->fetch();
				$comment = $row["komentar"];
				if($comment != NULL){
					$comment = $comment;
				}else{
					$comment = 100;
				}
			}else{
				$comment = 101;
			}
			return $comment;
		}else{
			return 102;
		}
	}
	
	function getAvgRatingForCandidat($canId, $pca_id){
		Global $db;
		$canId = intval($canId);
		$pca_id = intval($pca_id);
		if($canId != 0 AND $pca_id != 0){
			$query = $db->prepare("
				SELECT
					pca_avg_rating
				FROM 
					idk_pp_cand_appts
				WHERE 
					pca_kandidat_id = :pca_kandidat_id
					AND 
					pca_id = :pca_id
			");
			$query->execute(array(
				':pca_kandidat_id' => $canId,
				':pca_id' => $pca_id
			));
			$row = $query->fetch();
			$pca_avg_rating = intval($row["pca_avg_rating"]);
			if($pca_avg_rating != 0){
				return $pca_avg_rating;
			}else{
				return 0;
			}
		}else{
			return 102;
		}
	}
	
	function getSmjerNaziv($smjerId, $jezikVr){
		Global $db;
		$smjerId = intval($smjerId);
		$jezikVr = intval($jezikVr);
		if($smjerId != 0){
			if($jezikVr == 1){
				$uslovJezik = "ss_naziv_de";
			}elseif($jezikVr == 0){
				$uslovJezik = "ss_naziv";
			}else{
				$uslovJezik = "ss_naziv_en";
			}
			$query = $db->prepare("
				SELECT ".$uslovJezik." AS naziv
				FROM idk_skole_smjerovi
				WHERE ss_id = :ss_id
			");
			$query->execute(array(
				':ss_id' => $smjerId
			));
			if($query->rowCount() != 0){
				$row = $query->fetch();
				$smjerNaziv = $row["naziv"];
			}else{
				$smjerNaziv = "error101";
			}
		}else{
			$smjerNaziv = "error102";
		}
		return $smjerNaziv;
	}
	
	function getSkolaNaziv($skolaId, $jezikVr){
		Global $db;
		$skolaId = intval($skolaId);
		$jezikVr = intval($jezikVr);
		if($skolaId != 0){
			if($jezikVr == 1){
				$uslovJezik = "skola_naziv_de";
			}else{
				$uslovJezik = "skola_naziv";
			}
			$query = $db->prepare("
				SELECT ".$uslovJezik." AS naziv
				FROM idk_skole
				WHERE skola_id = :skola_id
			");
			$query->execute(array(
				':skola_id' => $skolaId
			));
			if($query->rowCount() != 0){
				$row = $query->fetch();
				$skolaNaziv = $row["naziv"];
			}else{
				$skolaNaziv = "error101";
			}
		}else{
			$skolaNaziv = "error102";
		}
		return $skolaNaziv;
	}
	
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

	function getPartnerForCandidateR($idCan){
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
			$kandidat_ppa_partner_id = intval($row["kandidat_ppa_partner_id"]);
		}else{
			$kandidat_ppa_partner_id = 0;
		}
		return $kandidat_ppa_partner_id;
	}
	
	//FUNKCIJA KOJA BROJI KANDIDATE NEKOG PARTNERA I VRAĆA TAJ BROJ ZAJEDNO SA BROJEM TRAŽENIH KANDIDATA
	//$povratni_array['max'] => broj trazenih kandidata
	//$povratni_array['val'] => trenutni broj kanddidata koji su vezani za tog partnera
	
	function countCandidatesByPartner($partner_id){	
		Global $db;
		$query_get_candidates_by_partner = $db -> prepare("
			SELECT COUNT(kan.kandidat_ppa_partner_id) as candidates_val, ppa_number_of_candidates  as candidates_max
			FROM idk_kandidati kan
			RIGHT JOIN idk_pp_partners ppa
			ON ppa.ppa_id = kan.kandidat_ppa_partner_id
			WHERE ppa.ppa_id = :partner_id
			GROUP BY kan.kandidat_ppa_partner_id
		");
		$query_get_candidates_by_partner -> execute(array(':partner_id' => $partner_id));
		
		$row_get_candidates_by_partner = $query_get_candidates_by_partner -> fetch();
		
		$return_array = array();
		
		$return_array['max'] = $row_get_candidates_by_partner['candidates_max'];
		$return_array['val'] = $row_get_candidates_by_partner['candidates_val'];


		if(is_null($return_array['max'])){$return_array['max'] = '0';}
		if(is_null($return_array['val'])){$return_array['val'] = '0';}
		
		return $return_array;
	}
	
	function getProvjeraJezikaKriterij($valueJezik, $nalogId){
		Global $db;
		$valueJezik = $valueJezik;
		$nalogId = intval($nalogId);
		$resultArray = array();
		if($nalogId != 0){
			$kriterijJezika = getNalogKriterij($nalogId);
			$kriterij = $kriterijJezika[2];
			if($kriterij != NULL){
				switch($kriterij){
					case "A1":
						$minBrojKriterij = 1;
					break;
					case "A2":
						$minBrojKriterij = 2;
					break;
					case "B1":
						$minBrojKriterij = 3;
					break;
					case "B2":
						$minBrojKriterij = 4;
					break;
					case "C1":
						$minBrojKriterij = 5;
					break;
					case "C2":
						$minBrojKriterij = 6;
					break;
					default:
						$minBrojKriterij = 0;
				}
				switch($valueJezik){
					case "A1":
						$minBrojJezik = 1;
					break;
					case "A2":
						$minBrojJezik = 2;
					break;
					case "B1":
						$minBrojJezik = 3;
					break;
					case "B2":
						$minBrojJezik = 4;
					break;
					case "C1":
						$minBrojJezik = 5;
					break;
					case "C2":
						$minBrojJezik = 6;
					break;
					default:
						$minBrojJezik = 0;
				}
				
				if($minBrojJezik >= $minBrojKriterij){
					if($valueJezik == "A1"){
						$slusanje_bg = "bg-success";
						$slusanje_style = "width: 17%;";
						$slusanje_value = 17;
					}else if($valueJezik == "A2"){
						$slusanje_bg = "bg-success";
						$slusanje_style = "width: 34%;";
						$slusanje_value = 34;
					}else if($valueJezik == "B1"){
						$slusanje_bg = "bg-success";
						$slusanje_style = "width: 50%;";
						$slusanje_value = 50;
					}else if($valueJezik == "B2"){
						$slusanje_bg = "bg-success";
						$slusanje_style = "width: 66%;";
						$slusanje_value = 66;
					}else if($valueJezik == "C1"){
						$slusanje_bg = "bg-success";
						$slusanje_style = "width: 83%;";
						$slusanje_value = 83;
					}else if($valueJezik == "C2"){
						$slusanje_bg = "bg-success";
						$slusanje_style = "width: 100%;";
						$slusanje_value = 100;
					}else{
						$slusanje_bg = "bg-success";
						$slusanje_style = "width: 1%;";
						$slusanje_value = 1;
					}
				}else{
					if($valueJezik == "A1"){
						$slusanje_bg = "bg-warning";
						$slusanje_style = "width: 17%;";
						$slusanje_value = 17;
					}else if($valueJezik == "A2"){
						$slusanje_bg = "bg-warning";
						$slusanje_style = "width: 34%;";
						$slusanje_value = 34;
					}else if($valueJezik == "B1"){
						$slusanje_bg = "bg-warning";
						$slusanje_style = "width: 50%;";
						$slusanje_value = 50;
					}else if($valueJezik == "B2"){
						$slusanje_bg = "bg-warning";
						$slusanje_style = "width: 66%;";
						$slusanje_value = 66;
					}else if($valueJezik == "C1"){
						$slusanje_bg = "bg-warning";
						$slusanje_style = "width: 83%;";
						$slusanje_value = 83;
					}else if($valueJezik == "C2"){
						$slusanje_bg = "bg-warning";
						$slusanje_style = "width: 100%;";
						$slusanje_value = 100;
					}else{
						$slusanje_bg = "bg-warning";
						$slusanje_style = "width: 1%;";
						$slusanje_value = 1;
					}
				}
				array_push($resultArray, $slusanje_bg, $slusanje_style, $slusanje_value);
				
			}else{
				if($valueJezik == "A1"){
					$slusanje_bg = "bg-warning";
					$slusanje_style = "width: 17%;";
					$slusanje_value = 17;
				}else if($valueJezik == "A2"){
					$slusanje_bg = "bg-warning";
					$slusanje_style = "width: 34%;";
					$slusanje_value = 34;
				}else if($valueJezik == "B1"){
					$slusanje_bg = "bg-primary";
					$slusanje_style = "width: 50%;";
					$slusanje_value = 50;
				}else if($valueJezik == "B2"){
					$slusanje_bg = "bg-primary";
					$slusanje_style = "width: 66%;";
					$slusanje_value = 66;
				}else if($valueJezik == "C1"){
					$slusanje_bg = "bg-success";
					$slusanje_style = "width: 83%;";
					$slusanje_value = 83;
				}else if($valueJezik == "C2"){
					$slusanje_bg = "bg-success";
					$slusanje_style = "width: 100%;";
					$slusanje_value = 100;
				}else{
					$slusanje_bg = "bg-primary";
					$slusanje_style = "width: 1%;";
					$slusanje_value = 1;
				}
				array_push($resultArray, $slusanje_bg, $slusanje_style, $slusanje_value);
			}
		}else{
			if($valueJezik == "A1"){
				$slusanje_bg = "bg-primary";
				$slusanje_style = "width: 17%;";
				$slusanje_value = 17;
			}else if($valueJezik == "A2"){
				$slusanje_bg = "bg-primary";
				$slusanje_style = "width: 34%;";
				$slusanje_value = 34;
			}else if($valueJezik == "B1"){
				$slusanje_bg = "bg-primary";
				$slusanje_style = "width: 50%;";
				$slusanje_value = 50;
			}else if($valueJezik == "B2"){
				$slusanje_bg = "bg-primary";
				$slusanje_style = "width: 66%;";
				$slusanje_value = 66;
			}else if($valueJezik == "C1"){
				$slusanje_bg = "bg-primary";
				$slusanje_style = "width: 83%;";
				$slusanje_value = 83;
			}else if($valueJezik == "C2"){
				$slusanje_bg = "bg-primary";
				$slusanje_style = "width: 100%;";
				$slusanje_value = 100;
			}else{
				$slusanje_bg = "bg-primary";
				$slusanje_style = "width: 1%;";
				$slusanje_value = 1;
			}
			
			array_push($resultArray, $slusanje_bg, $slusanje_style, $slusanje_value);
		}
		return $resultArray;
		
		unset($resultArray);
	}
	
	function updateGeneralInfoPCAArrayR($canId, $nalogId, $papId, $pcaId, $field, $value) {
		Global $db;
		$canId = intval($canId);
		$nalogId = intval($nalogId);
		$papId = intval($papId);
		$pcaId = intval($pcaId);
		$field = $field;

		$flagValue = 0;
		if ($field == "pca_recommendation") {
			$value = intval($value);
			$flagValue = (($value != 0) ? 1 : 0);
		} else if ($field == "pca_interviewer") {
			$value = $value;
			$flagValue = (($value != "") ? 1 : 0);
		} else if ($field == "pca_reason_recommendation") {
			$value = $value;
			$flagValue = (($value != "") ? 1 : 0);
		} else {
			$value = $value;
			$flagValue = (($value != "") ? 1 : 0);
		}

		$result = array(
			"status" => 102, 
			"avgUpdate" => 0
		);
		if($canId != 0 AND $nalogId != 0 AND $papId != 0 AND $pcaId != 0 AND $field != "" AND $flagValue == 1){
			$query = $db->prepare("
				UPDATE
					idk_pp_cand_appts
				SET 
					".$field." = :value_field
				WHERE 
					pca_id = :pca_id
					AND
					pca_kandidat_id = :pca_kandidat_id
			");
			$query->execute(array(
				':pca_id' => $pcaId,
				':pca_kandidat_id' => $canId,
				':value_field' => $value
			));

			$result["status"] = 1;
			$result["avgUpdate"] = generateAvgForCandidateQuestionR($canId, $nalogId, 0, $pcaId, $papId);
			
		}
		return $result;
	}

	function updateCommentPCA($canId, $pca_id, $commentVal){
		Global $db;
		$canId = intval($canId);
		$pca_id = intval($pca_id);
		if($canId != 0 AND $pca_id != 0 AND $commentVal != ""){
			$query = $db->prepare("
				UPDATE
					idk_pp_cand_appts
				SET 
					pca_comment = :pca_comment
				WHERE 
					pca_id = :pca_id
					AND
					pca_kandidat_id = :pca_kandidat_id
			");
			$query->execute(array(
				':pca_id' => $pca_id,
				':pca_kandidat_id' => $canId,
				':pca_comment' => $commentVal
			));
			return 1;
		}else{
			return 102;
		}
	}
	
	function getCommentPCA($canId, $pca_id){
		Global $db;
		$canId = intval($canId);
		$pca_id = intval($pca_id);
		if($canId != 0 AND $pca_id != 0){
			$query = $db->prepare("
				SELECT 
					pca_comment
				FROM
					idk_pp_cand_appts
				WHERE 
					pca_id = :pca_id
					AND
					pca_kandidat_id = :pca_kandidat_id
			");
			$query->execute(array(
				':pca_id' => $pca_id,
				':pca_kandidat_id' => $canId
			));
			$row = $query->fetch();
			$komment = $row["pca_comment"];
			if($komment != NULL){
				$komment = $komment;
			}else{
				$komment = 101;
			}
			return $komment;
		}else{
			return 102;
		}
	}

	function getInfoEnpalPCAArrayR($canId, $pca_id){
		Global $db;
		$canId = intval($canId);
		$pca_id = intval($pca_id);
		$result = array(
			"status" => 0, 
			"pca_comment" => '', 
			"pca_interviewer" => '', 
			"pca_recommendation" => 0, 
			"pca_reason_recommendation" => "",
		);
		if($canId != 0 AND $pca_id != 0){
			$query = $db->prepare("
				SELECT 
					pca_comment, 
					pca_interviewer, 
					pca_recommendation,
					pca_reason_recommendation
				FROM
					idk_pp_cand_appts
				WHERE 
					pca_id = :pca_id
					AND
					pca_kandidat_id = :pca_kandidat_id
			");
			$query->execute(array(
				':pca_id' => $pca_id,
				':pca_kandidat_id' => $canId
			));
			if ($query->rowCount() == 1){
				$row = $query->fetch();
				$pca_comment = $row["pca_comment"];
				$pca_interviewer = $row["pca_interviewer"];
				$pca_recommendation = intval($row["pca_recommendation"]);
				$pca_reason_recommendation = $row["pca_reason_recommendation"];
				$result["status"] = 1;
				$result["pca_comment"] = ( ($pca_comment != null) ? $pca_comment : '' );
				$result["pca_interviewer"] = ( ($pca_interviewer != null) ? $pca_interviewer : '' );
				$result["pca_recommendation"] = ( ($pca_recommendation != null) ? $pca_recommendation : 0 );
				$result["pca_reason_recommendation"] = ( ($pca_reason_recommendation != null) ? $pca_reason_recommendation : '' );
			} else {
				$result["status"] = 101;
			}
		}else{
			$result["status"] = 102;
		}
		return $result;
	}
	
	function getVozackaDozvolaKandidat($idKandidat){
		Global $db; 
		$idKandidat = intval($idKandidat);
		$rezultat = array();
		if($idKandidat != 0){
			$query = $db->prepare("
				SELECT 
					kandidat_vozacka_kategorija, kandidat_vozacka_dozvola
				FROM 
					idk_kandidati
				WHERE 
					kandidat_id = :kandidat_id
			");
			$query->execute(array(
				':kandidat_id' => $idKandidat
			));
			$row = $query->fetch();
			$kandidat_vozacka_kategorija = $row["kandidat_vozacka_kategorija"];
			$kandidat_vozacka_dozvola = $row["kandidat_vozacka_dozvola"];
			
			$kategorija_vozackeExp = array();
			if($kandidat_vozacka_kategorija == NULL){
				if($kandidat_vozacka_dozvola == "Da"){
					$kategorija_vozackeExp = explode(",", "B");
				}else{
					$kategorija_vozackeExp = explode(",", "Nema");
				}
			}else{
				$kategorija_vozackeExp = explode(",", $kandidat_vozacka_kategorija);
			}
			if(!in_array("Nema", $kategorija_vozackeExp)){
				if(in_array("CE", $kategorija_vozackeExp)){
					$nize_kategorije = array("B","C1","C","C1E","CE");
					foreach($nize_kategorije as $niza_kat){
						if(!in_array($niza_kat, $rezultat, true)){
							array_push($rezultat, $niza_kat);
						}
					}
				}
				if(in_array("C1E", $kategorija_vozackeExp)){
					$nize_kategorije = array("B","C1","C","C1E");
					foreach($nize_kategorije as $niza_kat){
						if(!in_array($niza_kat, $rezultat, true)){
							array_push($rezultat, $niza_kat);
						}
					}
				}
				if(in_array("C", $kategorija_vozackeExp)){
					$nize_kategorije = array("B","C1","C");
					foreach($nize_kategorije as $niza_kat){
						if(!in_array($niza_kat, $rezultat, true)){
							array_push($rezultat, $niza_kat);
						}
					}
				}
				if(in_array("C1", $kategorija_vozackeExp)){
					$nize_kategorije = array("B","C1");
					foreach($nize_kategorije as $niza_kat){
						if(!in_array($niza_kat, $rezultat, true)){
							array_push($rezultat, $niza_kat);
						}
					}
				}
				if(in_array("BE", $kategorija_vozackeExp)){
					$nize_kategorije = array("B","BE");
					foreach($nize_kategorije as $niza_kat){
						if(!in_array($niza_kat, $rezultat, true)){
							array_push($rezultat, $niza_kat);
						}
					}
				}
				if(in_array("B", $kategorija_vozackeExp)){
					$nize_kategorije = array("B");
					foreach($nize_kategorije as $niza_kat){
						if(!in_array($niza_kat, $rezultat, true)){
							array_push($rezultat, $niza_kat);
						}
					}
				}
			}else{
				array_push($rezultat, 100);
			}
		}else{
			array_push($rezultat, 102);
		}
		return $rezultat;
		unset($rezultat);
	}
	function getCandidateIdByKey($candidate_key){
		Global $db;
		$query_get_candidate_id = $db->prepare("
			SELECT kandidat_id 
			FROM idk_kandidati 
			WHERE kandidat_check = '$candidate_key'
		");
		
		$query_get_candidate_id -> execute();
		$row_get_candidate_id = $query_get_candidate_id -> fetch();
		
		$candidate_id = $row_get_candidate_id['kandidat_id'];
		
		return $candidate_id;
	}
	function getCandidateKeyById($candidate_id){
		// var_dump($candidate_id);
		Global $db;
		$query_get_candidate_key = $db->prepare("
			SELECT kandidat_check
			FROM idk_kandidati 
			WHERE kandidat_id = $candidate_id
		");
		// var_dump($query_get_candidate_key);
		
		$query_get_candidate_key -> execute();
		$row_get_candidate_key = $query_get_candidate_key -> fetch();
		$candidate_key = $row_get_candidate_key['kandidat_check'];
		// var_dump($row_get_candidate_key['kandidat_check']);
		
		return $candidate_key;
	}
	function setNameForDenis($name){
		$return_value = $name;
		if($name == 'Elektronik Hammer'){
			$return_value = "Partner 232";
		}
		else if($name == 'MFB-Com GmbH Mitteldeutscher Fernmeldebau'){
			$return_value = 'Partner 187';
		}
		else if($name == 'SSF Telekommunikations-Management GmbH'){
			$return_value = 'Partner 475';
		}
		else if($name == 'BFE Nachrichtentechnik GmbH'){
			$return_value = 'Partner 714';
		}
		else if($name == 'FKT-Berlin GMBH'){
			$return_value = 'Partner 518';
		}
		else if($name == 'Netzkontor KW 11'){
			$return_value = 'Auftrag 947';
		}
		else if($name == 'NKG Fiberservice GmbH'){
			$return_value = 'Partner 321';
		}
		return $return_value;
	}
	
	function checkBarIdForCandidatArrayR($idKan, $nalogId){
		Global $db;
		//Funkcija vraca kombinacije
		// 102 poruka greške - problem sa parametrima koji se daju funkciji 
		// 101 poruka - kandidat se ne moze prikazati na PP-u zbog statusa prijave
		$statusiPrijave = array(3,4,5,7,8,9,10,12,15,18,21,24,27); // statusi prijave preko kojih je moguc pregled na PPu
		$result = array(
			"type" => array(), 
			"barId" => array()
		);
		if(isset($idKan)){
			$queryKandidati = $db->prepare("
				SELECT
					kan.kandidat_status_prijave
				FROM 
					idk_kandidati kan
				WHERE 
					kan.kandidat_id = :kandidat_id 
					AND 
					kan.kandidat_status != 3
			");
			$queryKandidati->execute(array(
				':kandidat_id' => $idKan
			));
			$rowKandidati = $queryKandidati->fetch();
			$statusPrijave = intval($rowKandidati["kandidat_status_prijave"]);
			
			if(in_array($statusPrijave, $statusiPrijave) AND $nalogId != NULL){
				if($statusPrijave == 7){
					//Čeka ugovor
					array_push($result["type"], 2);
					array_push($result["barId"], 2);
				}else if($statusPrijave == 8){
					//Poslan ugovor
					array_push($result["type"], 2);
					array_push($result["barId"], 3);
				}else if(in_array($statusPrijave, array(9,12,15,18,21,24,27))){
					//Potpisan ugovor
					array_push($result["type"], 2);
					array_push($result["barId"], 4);
				}else if($statusPrijave == 10){
					//Poceo raditi
					array_push($result["type"], 2);
					array_push($result["barId"], 5);
				}else{
					//Za status prijave 3,4,5 (Casting, Zaposlen, Odbijen)
					$queryProjekti = $db->prepare("
						SELECT 
							SUM(CASE WHEN pro.project_name LIKE ('%Casting%') THEN 1 ELSE 0 END) AS brojCasting,
							SUM(CASE WHEN pro.project_name LIKE ('%Intervju%') THEN 1 ELSE 0 END) AS brojIntervju,
							SUM(CASE WHEN pro.project_name LIKE ('%Ugovor%') THEN 1 ELSE 0 END) AS brojUgovor,
							SUM(CASE WHEN pro.project_name LIKE ('%Odbijen%') THEN 1 ELSE 0 END) AS brojOdbijen
						FROM 
							idk_kandidati kan
						JOIN 
							idk_project_kandidati pk
						ON 
							pk.pk_kandidatid = kan.kandidat_id
						JOIN 
							idk_projects pro
						ON 
							pro.project_id = pk.pk_projectid
						WHERE 
							pro.project_nalogid = :project_nalogid
						AND 
							kan.kandidat_id = :kandidat_id 
						AND 
							kan.kandidat_status != 3
					");
					$queryProjekti->execute(array(
						':project_nalogid' => $nalogId, 
						':kandidat_id' => $idKan
					));
					
					$rowProjekti = $queryProjekti->fetch();
					$brojCasting = intval($rowProjekti["brojCasting"]); 
					$brojIntervju = intval($rowProjekti["brojIntervju"]); 
					$brojUgovor = intval($rowProjekti["brojUgovor"]); 
					$brojOdbijen = intval($rowProjekti["brojOdbijen"]);
					
					if($brojCasting != 0){
						array_push($result["type"], 1);
						array_push($result["barId"], 2);
					}else if($brojIntervju != 0){
						array_push($result["type"], 1);
						array_push($result["barId"], 3);
					}else if($brojUgovor != 0){
						array_push($result["type"], 1);
						array_push($result["barId"], 4);
					}else if($brojOdbijen != 0){
						array_push($result["type"], 1);
						array_push($result["barId"], 5);
					}else{
						array_push($result["type"], 101);
						array_push($result["barId"], 101);
					}
				}
			}else{
				array_push($result["type"], 101);
				array_push($result["barId"], 101);
			}
		}else{
			array_push($result["type"], 102);
			array_push($result["barId"], 102);
		}
		return $result;
		
		unset($statusiPrijave);
		unset($result);
	}
	
	function showCandidateIdR($userId){
		$showForUsers = array(18,19,37,43,1,3,136,5,84,265);
		//Rezultat je 
		//			1 - dozvoljen prikaz
		//			0 - nije dozvoljen prikaz
		if(in_array($userId, $showForUsers)){
			return 1;
		}else{
			return 0;
		}
	}
	
	function addToLogsStatusPrijave($stari_projekt_id, $novi_projekt_id, $status_id, $kandidat_id, $izvor, $is_from_queue = NULL){
		Global $db;
		Global $userId;
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
		
		updateCandidateProjectionAndInstallment($kandidat_id);
		
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
						':lsp_employee_id' => $userId,
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

	function isReservedByProject($project_name){
		$return_value	= false;
		if(		
				strpos($project_name, 'BOT - ispunjava uslove')
			OR	strpos($project_name, 'Završeni kandidati')
			OR	strpos($project_name, 'Obrađeno')
			OR	strpos($project_name, 'Intervju')
			OR	strpos($project_name, 'Casting')
			OR	strpos($project_name, 'Ugovor')
			OR	strpos($project_name, 'počeli')
			OR 	strpos($project_name, 'Baza - odgovara za nalog')
		){
			$return_value	= true;
		}
		return $return_value;
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

	function checkCandidateInputs($kandidat_id, $nalog_from_queue = null){
	
		Global $db;
		$query_kandidat = $db->prepare("
			SELECT * FROM idk_kandidati WHERE kandidat_id = $kandidat_id ");
		$query_kandidat->execute();
		$row_kandidat = $query_kandidat->fetch();
		$kandidat_vozacka = $row_kandidat['kandidat_vozacka_dozvola'];
		$kandidat_vozacka_kategorija = $row_kandidat['kandidat_vozacka_kategorija'];
		$kandidat_status_messenger = $row_kandidat['kandidat_status_messenger'];

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
	
	function hasPartnersR($nalogId){
		Global $db;
		//Funkcija vraca kombinacije
		// 102 poruka greške - problem sa parametrima koji se daju funkciji 
		// 101 poruka - ne postoji nalog sa tim ID-om u bazi
		// 0,1 - normalne vrijednosti (0 - nema partnere 1 - ima partnere)
		$nalogId = intval($nalogId);
		if($nalogId != 0){
			$query = $db->prepare("
				SELECT
					partneri_pp
				FROM 
					idk_nalozi
				WHERE 
					nalog_id = :nalog_id 
			");
			$query->execute(array(
				':nalog_id' => $nalogId
			));
			if($query->rowCount() != 0){
				$row = $query->fetch();
				return intval($row["partneri_pp"]);
			}else{
				return 101;
			}
		}else{
			return 102;
		}
	}
	
	function getWorkingPosition($nalogId){
		$nalogId = intval($nalogId);
		if($nalogId != 0){
			if($nalogId == 222){
				echo '
					<option value = "BK-Monteur">BK-Monteur</option>
					<option value = "FTTB Bau">FTTB Bau</option>
					<option value = "LKW-Fahrer u. Hilfsmonteur">LKW-Fahrer u. Hilfsmonteur</option>
					<option value = "LWL-Monteur">LWL-Monteur</option>
					<option value = "OIL-Fernmeldemonteur">OIL-Fernmeldemonteur</option>
					<option value = "OIL-Monteur (Quereinsteiger)">OIL-Monteur (Quereinsteiger)</option>
					<option value = "Telekom Service 2-Draht ">Telekom Service 2-Draht </option>
					<option value = "Telekom Service 2-Draht (MFB-Com)">Telekom Service 2-Draht (MFB-Com)</option>
					<option value = "Telekom Service FTTH">Telekom Service FTTH</option>
					<option value = "Tiefbau Facharbeiter">Tiefbau Facharbeiter</option>
				';
			}else if($nalogId == 213){
				echo '
					<option value = "Tiefbaufacharbeiter">Tiefbaufacharbeiter</option>
				';
			}else if($nalogId == 228){
				echo '
					<option value = "Tiefbaufacharbeiter">Tiefbaufacharbeiter</option>
					<option value = "Service techniker">Service techniker</option>
				';
			}else if($nalogId == 237){
				echo '
					<option value = "LWL-Monteure">LWL-Monteure</option>
					<option value = "Servicetechniker Koax">Servicetechniker Koax</option>
					<option value = "LKW-Fahrer mit Bereitschaft zu körperlicher Arbeit (OIL)">LKW-Fahrer mit Bereitschaft zu körperlicher Arbeit (OIL)</option>
					<option value = "FM-Monteure mit einschlägiger OIL-Erfahrung">FM-Monteure mit einschlägiger OIL-Erfahrung</option>
					<option value = "FM-Monteure mit einschlägiger Erfahrung">FM-Monteure mit einschlägiger Erfahrung</option>
					<option value = "Bauleiter">Bauleiter</option>
				';
			}else if($nalogId == 268){
				echo '
					<option value = "Tiefbau">Tiefbau</option>
					<option value = "Service techniker">Service techniker</option>
				';
			}else if($nalogId == 272){
				echo '
					<option value = "Servicemonteur Fahrzeugglas">Servicemonteur Fahrzeugglas</option>
				';
			}else{
				echo '';
			}
		}else{
			echo '';
		}
	}
	
	function getPartnerLocation($nalogId){
		if($nalogId != 0){
			if($nalogId == 222){
				echo '
					<option value = "Bundesweit">Bundesweit</option>
					<option value = "Baden-Württemberg">Baden-Württemberg</option>
					<option value = "Großraum Nordbayern (Metropolregion Nürnberg)">Großraum Nordbayern (Metropolregion Nürnberg)</option>
					<option value = "Rheinland-Pfalz">Rheinland-Pfalz</option>
					<option value = "Sachsen">Sachsen</option>
					<option value = "Thüringen">Thüringen</option>
					<option value = "Berlin">Berlin</option>
					<option value = "Chemnitz">Chemnitz</option>
					<option value = "Görlitz">Görlitz</option>
					<option value = "Hamburg">Hamburg</option>
					<option value = "Leipzig">Leipzig</option>
					<option value = "Plauen">Plauen</option>
				';
			}else if($nalogId == 213){
				echo '';
			}else{
				echo '';
			}
		}else{
			echo '';
		}
	}
	
	function getRejectReasonsArrayR($languageUser){
		Global $db;
		$languageUser = intval($languageUser);
		$tableColumn = "";
		if($languageUser == 1){
			$tableColumn = "rr_name_de";
		}elseif($languageUser == 2){
			$tableColumn = "rr_name_en";
		}else{
			$tableColumn = "rr_name_bs";
		}
		$result = array(
			"count" => array(),
			"id" => array(),
			"name" => array()
		);
		$cnt = 0;
		$query = $db->prepare("
			SELECT 
				rr_id, ".$tableColumn." AS rr_naziv
			FROM 
				idk_reject_reasons
			WHERE 
				rr_rejected_by = 2
				AND 
				rr_status = 0
			ORDER BY 
				rr_id
			ASC
		");
		$query->execute();
		if($query->rowCount() != 0){
			while($row = $query->fetch()){
				$rr_id = intval($row["rr_id"]);
				$rr_naziv = $row["rr_naziv"];
				
				array_push($result["count"], $cnt);
				array_push($result["id"], $rr_id);
				array_push($result["name"], $rr_naziv);
				$cnt++;
			}
			return $result;
		}else{
			return $result;
		}
		
		unset($result);
	}
	
	function getCandidateNacinOdlaska($candidateId){
		Global $db; 
		$candidateId = intval($candidateId);
		if($candidateId != 0){
			$query = $db->prepare("
				SELECT 
					kandidat_nacin_odlaska
				FROM 
					idk_kandidati
				WHERE 
					kandidat_id = :candidate_id
			");
			$query->execute(array(
				':candidate_id' => $candidateId
			));
			if($query->rowCount() == 1){
				$row = $query->fetch();
				return intval($row["kandidat_nacin_odlaska"]);
			}else{
				return 0; 
			}
		}else{
			return 0;
		}
	}

	function getCandidateFullRecognition($candidateId) {

		Global $db; 
		$candidateId = intval($candidateId); //kandidat posao id
	
		if ( $candidateId != 0 ) {
			
			$queryCheck = $db->prepare("
				SELECT 
					kandidat_dipl_id 
				FROM 
					idk_kandidati
				WHERE 
					kandidat_id = :kandidat_id 
			");  
	
			$queryCheck->execute(array(
				':kandidat_id' => $candidateId
			));
	
			$rowCheck = $queryCheck->fetch();
	
			$candidateDiplId = intval($rowCheck["kandidat_dipl_id"]); 
	
			$queryCondition = "";
	
			if( $candidateDiplId != 0 ) {
	
				$queryCondition = "AND id_cand_dipl = ".$candidateDiplId."";
	
			}
	
			$query = $db->prepare("
				SELECT 
					full_recognition
				FROM 
					idk_nostrifikovane_diplome
				WHERE 
					id_cand_job = :id_cand_job
					".$queryCondition."
			");
	
			$query->execute(array(
				':id_cand_job' => $candidateId
			));
	
			if ( $query->rowCount() == 1 ) {
	
				$row = $query->fetch();
	
				if ( $row["full_recognition"] != null ) {
					return $row["full_recognition"];
				}else {
					return 100;
				}
				
			} else {
	
				return 101;
	
			}
	
		} else {
	
			return 102;
	
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

	//REMINDER FUNCTION START
	function getUserTypeForReminderType($type){
		Global $db;
		$type = intval($type);
		//return 
		//		1 - superadmin; 
		//		2 - admin; 
		//		3 - PM;
		$query = $db->prepare("
			SELECT 
				prt_user_type
			FROM 
				idk_pp_reminder_types
			WHERE 
				prt_id = :prt_id
		");
		$query->execute(array(
			':prt_id' => $type
		));
		$row = $query->fetch();
		$userType = intval($row["prt_user_type"]);
		return $userType;
	}
	
	function getPuaIdForUser($type, $nalogId, $partnerId){
	
		Global $db; 
		Global $userId; 
		// var_dump($userId);
		$type = intval($type);
		$nalogId = intval($nalogId);
		$partnerId = intval($partnerId);
		
		$partnerCondition = "";
		if($partnerId != 0){
			$partnerCondition = " pua_partner_id = ".$partnerId." ";
		}else{
			$partnerCondition = " pua_partner_id is null ";
		}
		$query = $db->prepare("
			SELECT 
				pua_id
			FROM 
				idk_pp_user_access
			WHERE 
				pua_user_id = :pua_user_id
				AND 
				pua_nalog_id = :pua_nalog_id
				AND 
				".$partnerCondition."
				AND 
				pua_type = :pua_type
		");
		$query->execute(array(
			':pua_user_id' => $userId,
			':pua_nalog_id' => $nalogId,
			':pua_type' => $type
		));
		$row = $query->fetch();
		$puaId = intval($row["pua_id"]);
		
		// var_dump($query);
		// var_dump($userId);
		// var_dump($nalogId);
		// var_dump($type);
		// var_dump($puaId);
		return $puaId;
	}
	
	function getUserIdForPuaId($puaId){
		Global $db;
		$puaId = intval($puaId);
		
		$query = $db->prepare("
			SELECT 
				pua_user_id
			FROM 
				idk_pp_user_access
			WHERE 
				pua_id = :pua_id 
		");
		$query->execute(array(
			':pua_id' => $puaId
		));
		$row = $query->fetch();
		$user = intval($row["pua_user_id"]);
		
		return $user;
	}

	function reminderTypeHasDocumentsR($reminderType){
		Global $db; 
		$reminderType = intval($reminderType); 
		$query = $db->prepare("
			SELECT 
				prt_has_documents
			FROM 
				idk_pp_reminder_types
			WHERE 
				prt_id = :prt_id
		");
		$query->execute(array(
			':prt_id' => $reminderType
		));
		if($query->rowCount() == 1){
			$row = $query->fetch();
			return intval($row["prt_has_documents"]); 
		}else{
			return 100;
		}
	}

	function getDocumentTypeNameForReminderR($reminderType, $docId){
		Global $db; 
		Global $userId;

		$tipoviNrd = array(10,11,12,13);
		$tipoviCrd = array(20,21,22,23);
		$docId = intval($docId);
		$lang = getLanguageForUser($userId);

		if($docId != 0){
			if(in_array($reminderType, $tipoviNrd)){
				$columnName = "";
				if($lang == 1){
					$columnName = "dt.doc_type_name_de";
				}else{
					$columnName = "dt.doc_type_name";
				}
				$query = $db->prepare("
					SELECT 
						".$columnName." AS doc_name
					FROM 
						idk_pp_document_types dt
					JOIN 
						idk_pp_nalog_required_documents nrd
					ON 
						nrd.nrd_type_id = dt.doc_type_id
					WHERE 
						nrd.nrd_id = :docId
				");
				$query->execute(array(
					':docId' => $docId
				));
				if($query->rowCount() == 1){
					$row = $query->fetch();
					return $row["doc_name"];
				}else{
					return "";
				}
			}else if(in_array($reminderType, $tipoviCrd)){
				$columnName = "";
				if($lang == 1){
					$columnName = "dt.doc_type_name_de";
				}else{
					$columnName = "dt.doc_type_name";
				}
				$query = $db->prepare("
					SELECT 
						".$columnName." AS doc_name
					FROM 
						idk_pp_document_types dt
					JOIN 
						idk_pp_cand_required_documents crd
					ON 
						crd.crd_type_id = dt.doc_type_id
					WHERE 
						crd.crd_id = :docId
				");
				$query->execute(array(
					':docId' => $docId
				));
				if($query->rowCount() == 1){
					$row = $query->fetch();
					return $row["doc_name"];
				}else{
					return "";
				}
			}else{
				return "";
			}
		}else{
			return "";
		}
	}

	function reminderIdHasDocumentsR($reminderId){
		Global $db;
		/*
			Kombinacije rezultata 
			1		-		Reminder ima dokumente
			0		-		Reminder nema dokumente
			101		-		Poslani neodgovarajući parametri funkciji
			102		-		Za reminder query nije pronašao rezultat
			103		-		Kod remindera neispravan unos strani ključeva reminder settings i document
		*/
		$reminderId = intval($reminderId);
		if($reminderId != 0){
			$query = $db->prepare("
				SELECT 
					pr_reminder_setting_id,
					pr_reminder_document_id
				FROM 
					idk_pp_reminders
				WHERE 
					pr_id = :pr_id
			");
			$query->execute(array(
				':pr_id' => $reminderId
			));
			if($query->rowCount() == 1){
				$row = $query->fetch();
				$reminderSettingId = intval($row["pr_reminder_setting_id"]);
				$reminderDocumentId = intval($row["pr_reminder_document_id"]);
				if($reminderSettingId == 0 AND $reminderDocumentId != 0){
					return 1;
				}else if($reminderSettingId != 0 AND $reminderDocumentId == 0){
					return 0;
				}else{
					return 103;
				}
			}else{
				return 102;
			}
		}else{
			return 101;
		}
	}

	function getAccessControlArrayR($idKandidat, $nalogId, $partnerId, $remiderType, $docId = 0){
		Global $db; 
		Global $userId;
		$lang = getLanguageForUser($userId);
		$idKandidat = getCandidateIdByKey($idKandidat);
		include("language/language.php");  

		if($userId == 0){
			$result = array(
				"id" => 0,
				"status" => 104,
				"assigned" => 0, 
				"isLogged" => 0,
				"message" => $txtArray["Istekla Vam je sesija."][$lang],
				"title" => $txtArray["Prijava"][$lang]
			);
			
			return $result;
		}
		// var_dump($idKandidat);
		// var_dump($nalogId);
		// var_dump($partnerId);
		// var_dump($remiderType);
		//KOMBINACIJE REZULTATA
		
		//{"id":0,"status":101,"assigned":0,"isLogged":0,"message":"undefined"} 												-> ne postoji aktivan reminder za tog kandidata i svi mogu raditi sve
		//{"id":0,"status":102,"assigned":0,"isLogged":0,"message":"undefined"} 												-> GREŠKA: Za tog kandidata postoji više aktivnih remindera
		//{"id":0,"status":103,"assigned":0,"isLogged":0,"message":"Postoji Reminder koji nije vama namijenjen!"} 				-> Postoji reminder ali se user ne nalazi u koloni usera koji to trebaju izvrsiti
		//{"id":remID,"status":3,"assigned":vrIDuser,"isLogged":0,"message":"Ovaj reminder je završen od strane NN usera"} 		-> Zavrsen reminder od strane usera koji je razlicit logiranom useru
		//{"id":remID,"status":3,"assigned":vrIDuser,"isLogged":1,"message":"Ovaj reminder je završen od strane NN usera"} 		-> Zavrsen reminder od logiranog usera
		//{"id":remID,"status":2,"assigned":vrIDuser,"isLogged":0,"message":"Ovaj reminder je prihvacen od strane NN usera"} 	-> Prihvacen reminder od strane usera koji je razlicit logiranom useru
		//{"id":remID,"status":2,"assigned":vrIDuser,"isLogged":1,"message":"Ovaj reminder je prihvacen od strane NN usera"} 	-> Prihvacen reminder od logiranog usera
		//{"id":remID,"status":1,"assigned":0,"isLogged":0,"message":"undefined"} 												-> Poslan reminder i moze se dodijeliti useru
		
		
		//DESC
		//	isLogged	-> setovat ce se vrijednost u zavisnosti od toga da li je logirani user prihvatio ili nije moguce vrijednosti: 0 - nije, 1 - jeste
		//	assigned	-> setovat ce se id usera koji je prihvatio reminder
		//	status		-> moguce vrijednosti: 101 - ne postoji reminder, 102 - vise aktivnih remindera, 103 - postoji reminder ali nije poslan tom useru,  0 - nije prihvacen/zavrsen, 1 - prihvacen je ili zavrsen
		$idResult = 0;
		$statusResult = 0;
		$assignedResult = 0;
		$isLoggedResult = 0;
		$messageResult = "";
		$titleResult = "";

		//Tipovi remindera za nrd_dokumente i crd_dokumente
		$tipoviNrd = array(10,11,12,13);
		$tipoviCrd = array(20,21,22,23);
		
		//DESC
		//	assigned	-> setovat ce se id usera koji je prihvatio reminder
		//	status		-> moguce vrijednosti: 0 - nije prihvacen/zavrsen, 1 - prihvacen je ili zavrsen
		
		$idKandidat = intval($idKandidat);
		$nalogId = intval($nalogId);
		$partnerId = intval($partnerId);
		$remiderType = intval($remiderType);
		$docId = intval($docId);
		
		$partnerCondition = "";
		if($partnerId == 0 OR $remiderType == 4){
			$partnerCondition = " prs.prs_partner_id is null ";
		}else{
			$partnerCondition = " prs.prs_partner_id = ".$partnerId." ";
		}
		$userType = getUserTypeForReminderType($remiderType); 		//Uzima se na osnovu reminder type - kojem tipu usera se salje reminder
		if($remiderType == 4){
			$puaId = getPuaIdForUser(1, $nalogId, 0);
			if(!$puaId){
				$puaId = getPuaIdForUser($userType, $nalogId, $partnerId);	//Na osnovu userType, naloga i partnera uzima se puaId usera
			}
		}
		else{			
			$puaId = getPuaIdForUser($userType, $nalogId, $partnerId);	//Na osnovu userType, naloga i partnera uzima se puaId usera
		}
		
		//PROVJERA ZA DOKUMENTE START 
		$reminderHasDocuments = reminderTypeHasDocumentsR($remiderType);
		$sqlQuery = "";
		$docName = "";
		if($reminderHasDocuments == 1){
			if($docId != 0){
				$docName = getDocumentTypeNameForReminderR($remiderType, $docId);
				if(in_array($remiderType, $tipoviNrd)){
					$sqlQuery = "
						SELECT 
							pr.pr_id AS id, 
							pr.pr_status AS statusR, 
							pr.pr_users_sent AS sent, 
							pr.pr_user_assigned AS assigned, 
							prt.prt_notification_text_de AS title
						FROM 
							idk_pp_reminder_types prt
						JOIN 
							idk_pp_reminder_settings prs
						ON 
							prt.prt_id = prs.prs_reminder_type_id
						JOIN 
							idk_pp_reminder_documents prd
						ON 
							prs.prs_id = prd.prd_prs_id
							AND 
							prd.prd_crd_id is null 
							AND 
							prd.prd_nrd_id = ".$docId."
						JOIN 
							idk_pp_reminders pr
						ON 
							prd.prd_id = pr.pr_reminder_document_id
							AND 
							pr.pr_reminder_setting_id is null
						WHERE 
							prt.prt_id = :remiderType
							AND 
							prs.prs_nalog_id = :nalogId
							AND 
							".$partnerCondition."
							AND 
							pr.pr_candidate_id = :idKandidat
							AND 
							pr.pr_status IN (1,2,3)
					";
				}else if(in_array($reminderType, $tipoviCrd)){
					$sqlQuery = "";
				}else{
					$sqlQuery = "";
				}
			}else{
				$sqlQuery = "";
			}
		}else if($reminderHasDocuments == 0){
			$sqlQuery = "
				SELECT 
					pr.pr_id AS id, 
					pr.pr_status AS statusR, 
					pr.pr_users_sent AS sent, 
					pr.pr_user_assigned AS assigned, 
					prt.prt_notification_text_de AS title
				FROM 
					idk_pp_reminder_types prt
				JOIN 
					idk_pp_reminder_settings prs
				ON 
					prt.prt_id = prs.prs_reminder_type_id
				JOIN 
					idk_pp_reminders pr
				ON 
					prs.prs_id = pr.pr_reminder_setting_id 
				WHERE 
					prt.prt_id = :remiderType
					AND 
					prs.prs_nalog_id = :nalogId
					AND 
					".$partnerCondition."
					AND 
					pr.pr_candidate_id = :idKandidat
					AND 
					pr.pr_status IN (1,2,3)
			";
		}else{
			$sqlQuery = "";
		}

		unset($tipoviNrd);
		unset($tipoviCrd);
		//PROVJERA ZA DOKUMENTE END 

		if($sqlQuery != ""){
			$query = $db->prepare("
				".$sqlQuery."
			");

			$query->execute(array(
				':remiderType' => $remiderType,
				':nalogId' => $nalogId,
				':idKandidat' => $idKandidat
			));
			
			$cntRow = $query->rowCount();
			
			if($cntRow != 0){
				if($cntRow == 1){
					$row = $query->fetch();
					$id = intval($row["id"]);
					$status = intval($row["statusR"]);
					$sent = explode(",", $row["sent"]);
					if(in_array($status, array(2,3))){
						$assigned = getUserIdForPuaId($row["assigned"]);
					}else{
						$assigned = 0;
					}
					if($docName != ""){
						$title = $row["title"]." ".$docName;
					}else{
						$title = $row["title"];
					}
					
					if(in_array($puaId, $sent)){
						// var_dump('no');
						if($status == 1){
							$idResult = $id;
							$isLoggedResult = 0;
							$assignedResult = 0;
							$statusResult = 1;
							$messageResult = "undefined";
							$titleResult = $title;
						}else if($status == 2){
							$idResult = $id;
							$assignedResult = $assigned;
							$statusResult = 2;
							if($assigned == $userId){
								$isLoggedResult = 1;
							}else{
								$isLoggedResult = 0;
							}
							if($lang == 1){
								$messageResult = str_replace("{{username}}", "".getFirstAndLastNameUserR($assigned)."", $txtArray["Ovaj reminder je prihvaćen od strane"][$lang]);
							}else{
								$messageResult = $txtArray["Ovaj reminder je prihvaćen od strane"][$lang]." ".getFirstAndLastNameUserR($assigned)."";
							}
							
							$titleResult = $title;
						}else{
							$idResult = $id;
							$assignedResult = $assigned;
							$statusResult = 3;
							if($assigned == $userId){
								$isLoggedResult = 1;
							}else{
								$isLoggedResult = 0;
							}
							if($lang == 1){
								$messageResult = str_replace("{{username}}", "".getFirstAndLastNameUserR($assigned)."", $txtArray["Ovaj reminder je završen od strane"][$lang]);
							}else{
								$messageResult = $txtArray["Ovaj reminder je završen od strane"][$lang]." ".getFirstAndLastNameUserR($assigned)."";
							}
							$titleResult = $title;
						}
					}
					else{
						// var_dump('jes');
						if ($status == 3 AND $remiderType == 8) {
							$idResult = $id;
							$assignedResult = $assigned;
							$statusResult = 3;
							if($assigned == $userId){
								$isLoggedResult = 1;
							}else{
								$isLoggedResult = 0;
							}
							if($lang == 1){
								$messageResult = str_replace("{{username}}", "".getFirstAndLastNameUserR($assigned)."", $txtArray["Ovaj reminder je završen od strane"][$lang]);
							}else{
								$messageResult = $txtArray["Ovaj reminder je završen od strane"][$lang]." ".getFirstAndLastNameUserR($assigned)."";
							}
							$titleResult = $title;
						} else {
							$idResult = $id;
							$isLoggedResult = 0;
							$assignedResult = 0;
							$statusResult = 103;
							$messageResult = $txtArray["Postoji Reminder koji nije vama namijenjen!"][$lang];
							$titleResult = $title;
						}
					}
				}else{
					$idResult = 0;
					$isLoggedResult = 0;
					$assignedResult = 0;
					$statusResult = 102;
					$messageResult = "undefined";
					$titleResult = "undefined";
				}
			}else{
				$idResult = 0;
				$isLoggedResult = 0;
				$assignedResult = 0;
				$statusResult = 101;
				$messageResult = "undefined";
				$titleResult = "undefined";
			}
		}else{
			$idResult = 0;
			$isLoggedResult = 0;
			$assignedResult = 0;
			$statusResult = 101;
			$messageResult = "undefined";
			$titleResult = "undefined";
		}
		
		$result = array(
			"id" => $idResult,
			"status" => $statusResult,
			"assigned" => $assignedResult, 
			"isLogged" => $isLoggedResult,
			"message" => $messageResult,
			"title" => $titleResult
		);
		
		return $result;
		
		unset($result);
		unset($txtArray);
	}
	
	function updateReminderStatus($reminderId, $status){
		Global $db;
		Global $userId;
		//Kombinacije rezultata
		//101	-	Nisu poslani odgovarajući parametri funkciji
		//102	-	Status koji nije pokriven u funkciji. Pokrivene vrijednosti su: 2, 3
		//103	- 	User id = 0
		// 2    - 	Prihavaćen
		// 3    - 	Zavrsen
		$result = array(
			"status" => array(),
			"message" => array()
		);
		$lang = getLanguageForUser($userId);
		include("language/language.php");  
		if($userId == 0){
			array_push($result["status"], 103);
			array_push($result["message"], $txtArray["Istekla Vam je sesija."][$lang]);
			return $result;
			unset($result);
			unset($txtArray);
		}
		$reminderId = intval($reminderId);
		$status = intval($status);
		
		if($reminderId != 0 AND $status != 0){
			
			$reminderIdHasDocuments = reminderIdHasDocumentsR($reminderId);

			if(in_array($reminderIdHasDocuments, array(0,1))){
				if($reminderIdHasDocuments == 1){
					$queryGetReminderType = $db -> prepare('
						SELECT 
							prs.prs_reminder_type_id
						FROM  
							idk_pp_reminder_settings prs
						JOIN 
							idk_pp_reminder_documents prd 
						ON 
							prs.prs_id = prd.prd_prs_id
						JOIN 
							idk_pp_reminders pr
						ON 
							prd.prd_id = pr.pr_reminder_document_id
						WHERE 
							pr.pr_id = :reminderId
					');
				}else{
					$queryGetReminderType = $db -> prepare('
						SELECT 
							prs.prs_reminder_type_id
						FROM  
							idk_pp_reminder_settings prs
						JOIN 
							idk_pp_reminders pr
						ON 
							prs.prs_id = pr.pr_reminder_setting_id
						WHERE 
							pr.pr_id = :reminderId
					');
				}
				$queryGetReminderType -> execute(array(':reminderId' => $reminderId));
				$rowGetReminderType = $queryGetReminderType -> fetch();
				$reminderType = $rowGetReminderType['prs_reminder_type_id'];
			}else{
				array_push($result["status"], 101);
				array_push($result["message"], "ERROR 101: Invalid parameters sent to function.");
				return $result;
			}

			//Reminder info - mora ovako jer je potrebno iscupati PuaId za tog usera
			$sql = "";
			if($reminderIdHasDocuments == 1){
				$sql = "
					SELECT 
						prs.prs_nalog_id, prs.prs_partner_id, prt.prt_user_type, pr.pr_user_assigned, pr.pr_date_assigned
					FROM 
						idk_pp_reminder_settings prs
					JOIN 
						idk_pp_reminder_types prt
					ON 
						prs.prs_reminder_type_id = prt.prt_id
					JOIN 
						idk_pp_reminder_documents prd
					ON 
						prs.prs_id = prd.prd_prs_id
					JOIN 
						idk_pp_reminders pr
					ON 
						prd.prd_id = pr.pr_reminder_document_id
					WHERE 
						pr.pr_id = :reminderId
				";
			}else{
				$sql = "
					SELECT 
						prs.prs_nalog_id, prs.prs_partner_id, prt.prt_user_type, pr.pr_user_assigned, pr.pr_date_assigned
					FROM 
						idk_pp_reminder_settings prs
					JOIN 
						idk_pp_reminder_types prt
					ON 
						prs.prs_reminder_type_id = prt.prt_id
					JOIN 
						idk_pp_reminders pr
					ON 
						prs.prs_id = pr.pr_reminder_setting_id
					WHERE 
						pr.pr_id = :reminderId
				";
			}
			$query1 = $db->prepare($sql);
			$query1->execute(array(
				':reminderId' => $reminderId
			));
			$row1 = $query1->fetch();
			$nalogId = intval($row1["prs_nalog_id"]);
			$partnerId = intval($row1["prs_partner_id"]);
			$userType = intval($row1["prt_user_type"]);
			$userAssigned = intval($row1["pr_user_assigned"]);
			$dateAssigned = date("Y-m-d", strtotime($row1["pr_date_assigned"]));
			
			$puaId = getPuaIdForUser(1, $nalogId, 0);
			if($reminderType == 4){
				$puaId = getPuaIdForUser(1, $nalogId, 0);
				if(!$puaId){
					$puaId = getPuaIdForUser($userType, $nalogId, $partnerId);	//Na osnovu userType, naloga i partnera uzima se puaId usera
				}
			}
			else{			
				$puaId = getPuaIdForUser($userType, $nalogId, $partnerId);	//Na osnovu userType, naloga i partnera uzima se puaId usera
			}

			if($status == 2){
				$queryUpdate = $db->prepare("
					UPDATE 
						idk_pp_reminders
					SET
						pr_status = 2,
						pr_user_assigned = :puaId, 
						pr_date_assigned = :date 
					WHERE 
						pr_id  = :pr_id 
				");
				$queryUpdate->execute(array(
					':pr_id' => $reminderId,
					':puaId' => $puaId,
					':date' => date("Y-m-d H:i:s")
				));
				$logDesc = "PRIHVACEN REMINDER PP - Korisnik ID = [".$userId."] je prihvatio reminder ID = [".$reminderId."]";
				addToLogs($logDesc);
				array_push($result["status"], 2);
				array_push($result["message"], $txtArray["Prihvatili ste reminder."][$lang]);
				return $result;
			}else if($status == 3){
				if($userAssigned != 0 AND $dateAssigned != ""){
					if($userAssigned != $puaId){
						$queryUpdate = $db->prepare("
							UPDATE 
								idk_pp_reminders
							SET 
								pr_status = 3,
								pr_user_assigned = :puaId,
								pr_date_completed = :date
							WHERE 
								pr_id  = :pr_id
						");
						$queryUpdate->execute(array(
							':pr_id' => $reminderId,
							':puaId' => $puaId,
							':date' => date("Y-m-d H:i:s")
						));
					}else{
						$queryUpdate = $db->prepare("
							UPDATE 
								idk_pp_reminders
							SET 
								pr_status = 3,
								pr_date_completed = :date
							WHERE 
								pr_id  = :pr_id
						");
						$queryUpdate->execute(array(
							':pr_id' => $reminderId,
							':date' => date("Y-m-d H:i:s")
						));
					}
					$logDesc = "IZVRSEN REMINDER PP - Korisnik ID = [".$userId."] je izvršio reminder ID = [".$reminderId."]";
				}else{
					$queryUpdate = $db->prepare("
						UPDATE 
							idk_pp_reminders
						SET 
							pr_status = 3,
							pr_date_completed = :date,
							pr_user_assigned = :puaId,
							pr_date_assigned = :date
						WHERE 
							pr_id  = :pr_id
					");
					$queryUpdate->execute(array(
						':pr_id' => $reminderId,
						':puaId' => $puaId,
						':date' => date("Y-m-d H:i:s")
					));
					$logDesc = "PRIHVACEN I IZVRSEN REMINDER PP - Korisnik ID = [".$userId."] je prihvatio i izvršio reminder ID = [".$reminderId."]";
				}
				addToLogs($logDesc);
				array_push($result["status"], 3);
				array_push($result["message"], $txtArray["Završili ste reminder."][$lang]);
				return $result;
			}else{
				array_push($result["status"], 102);
				array_push($result["message"], "ERROR 101: Invalid status parameter sent to function. The correct values are 2 (accepted) or 3 (completed).");
				return $result;
			}
		}else{
			array_push($result["status"], 101);
			array_push($result["message"], "ERROR 101: Invalid parameters sent to function.");
			return $result;
		}
		unset($result);
		unset($txtArray);
	}

	function getCompanyAccessR($nalogId){
		
		Global $db; 
		$companyIdsForAccess = array(1204); 
		$nalogId = intval($nalogId); 
		/*
			companyIdsForAccess - je niz vrijednosti u koje se upisuju IDs kompanija koje imaju pristup za pitanja po Enpal zahtjevima
			Ako se nekad kasnije pojavi da nekoj kompaniji treba odobriti pristup kao i enpalu - samo upisati ID i voditi racuna oko setup-a pitanja
		*/ 
		if ($nalogId != 0) {

			$companyId = getCompanyIdFromNalogR($nalogId);

			if ($companyId != "Undefined") {

				if (in_array($companyId, $companyIdsForAccess)) {

					return 1;

				} else {

					return 0;

				}

			} else {

				return 0;

			}
			
		} else {

			return 0;

		}

	}

	function getAllPuaIdsForUserArrayR(){
		Global $db;
		Global $userId;
		
		$result = array();
		
		if($userId != 0){
			$query = $db->prepare("
				SELECT 
					pua_id 
				FROM 
					idk_pp_user_access 
				WHERE 
					pua_user_id = :pua_user_id
					AND 
					pua_status = 1
			");
			$query->execute(array(
				':pua_user_id' => $userId
			));
			while($row = $query->fetch()){
				$puaId = intval($row["pua_id"]);
				array_push($result, $puaId);
			}
			return $result;
		}else{
			return $result;
		}
		
		unset($result);
	}
	
	function getRemindersArrayR($selected_reminders){
		Global $db;
		Global $userId;
		$languageUser = getLanguageForUser($userId);

		$result = array(
			"count" => array(),
			"reminderId" => array(),
			"reminderLevel" => array(),
			"reminderText" => array(),
			"reminderType" => array(),
			"candidateId" => array(),
			"candidateCheck" => array(),
			"candidateFname" => array(),
			"candidateLname" => array(),
			"reminderStatus" => array(),
			"reminderDate" => array(),
			"nalogId" => array(),
			"partnerId" => array(),
		);
		
		if($userId != 0){
			$puaIdsExp = getAllPuaIdsForUserArrayR();
			$puaIdsImp = implode(",",$puaIdsExp);
			$puaIdsConditionArray = array();
			foreach($puaIdsExp AS $puaIdVal){
				array_push($puaIdsConditionArray, "(pr.pr_users_sent LIKE '".$puaIdVal."' OR pr.pr_users_sent LIKE '".$puaIdVal.",%' OR pr.pr_users_sent LIKE '%,".$puaIdVal.",%' OR pr.pr_users_sent LIKE '%,".$puaIdVal."')");
			}
			$puaIdsCondition = implode(" OR ", $puaIdsConditionArray);
			if(count($puaIdsConditionArray) != 0){
				$query = $db->prepare("
					SELECT 
						pr.pr_id,
						pr.pr_level, 
						pr.pr_candidate_id, 
						kan.kandidat_check, 
						kan.kandidat_ime, 
						kan.kandidat_prezime, 
						pr.pr_date_sent, 
						pr.pr_date_assigned, 
						pr.pr_status, 
						prs.prs_nalog_id, 
						prs.prs_partner_id, 
						prs.prs_reminder_type_id, 
						prt.prt_notification_text_de,
						NULL as doc_nalog_type_de,
						NULL as doc_nalog_type,
						NULL as doc_candidate_type_de,
						NULL as doc_candidate_type
					FROM 
						idk_pp_reminders pr 
						JOIN idk_pp_reminder_settings prs ON pr.pr_reminder_setting_id = prs.prs_id 
						JOIN idk_pp_reminder_types prt ON prs.prs_reminder_type_id = prt.prt_id 
						JOIN idk_kandidati kan ON pr.pr_candidate_id = kan.kandidat_id 
					WHERE 
						(
							pr.pr_status = 1 
							OR (
								pr.pr_status = 2 
								AND pr.pr_user_assigned IN (".$puaIdsImp.")
							)
						) 
						AND 
						(
							".$puaIdsCondition."
						)
						AND prs.prs_active = 1 
						AND prt.prt_id IN (".$selected_reminders.")

					UNION

					SELECT 

						rem.pr_id, 
						rem.pr_level,
						rem.pr_candidate_id, 
						kan.kandidat_check, 
						kan.kandidat_ime, 
						kan.kandidat_prezime, 
						rem.pr_date_sent, 
						rem.pr_date_assigned, 
						rem.pr_status, 
						settings.prs_nalog_id, 
						settings.prs_partner_id, 
						settings.prs_reminder_type_id, 
						reminder_types.prt_notification_text_de,
						nalog_doc_types.doc_type_name_de as doc_nalog_type_de,
						nalog_doc_types.doc_type_name as doc_nalog_type,
						candidate_doc_types.doc_type_name_de as doc_candidate_type_de,
						candidate_doc_types.doc_type_name as doc_candidate_type

					FROM (
						SELECT *
						FROM idk_pp_reminders pr
						WHERE 
						pr.pr_reminder_document_id IS NOT NULL
						AND
						(
							pr.pr_status = 1 
							OR 
							(
								pr.pr_status = 2 
								AND 
								pr.pr_user_assigned IN (".$puaIdsImp.")
							)
						)
						AND 
						(
							".$puaIdsCondition."
						) 
					) rem

					LEFT JOIN idk_pp_reminder_documents doc
					ON rem.pr_reminder_document_id = doc.prd_id

					LEFT JOIN idk_pp_cand_required_documents can_docs
					ON can_docs.crd_id = rem.pr_reminder_document_id

					JOIN (
						SELECT * 
						FROM idk_pp_reminder_settings prs
						WHERE prs.prs_active = 1 
					)settings
					ON settings.prs_id = doc.prd_prs_id 

					JOIN (
						SELECT * 
						FROM idk_pp_reminder_types prt
						WHERE prt.prt_id IN (".$selected_reminders.")
					) reminder_types 
					ON reminder_types.prt_id = settings.prs_reminder_type_id

					JOIN idk_kandidati kan
					ON kan.kandidat_id = rem.pr_candidate_id

					LEFT JOIN idk_pp_nalog_required_documents nalog_docs
					ON nalog_docs.nrd_id = doc.prd_nrd_id

					LEFT JOIN idk_pp_document_types nalog_doc_types
					ON nalog_doc_types.doc_type_id = nalog_docs.nrd_type_id

					LEFT JOIN idk_pp_document_types candidate_doc_types
					ON candidate_doc_types.doc_type_id = can_docs.crd_type_id

					ORDER BY pr_level DESC
				");
				// var_dump($query);
				// exit();
				$query->execute();
				$cntQuery = $query->rowCount();
				if($cntQuery != 0){
					$count = 0;
					while($row = $query->fetch()){
						
						$reminderId = intval($row["pr_id"]);
						$reminderLevel = intval($row["pr_level"]);
						$reminderText = $row["prt_notification_text_de"];
						$reminderType = intval($row["prs_reminder_type_id"]);
						$candidateId = intval($row["pr_candidate_id"]);
						$candidateCheck = $row["kandidat_check"];
						$candidateFname = $row["kandidat_ime"];
						$candidateLname = $row["kandidat_prezime"];
						$reminderStatus = intval($row["pr_status"]);
						if($reminderStatus == 1){
							$reminderDate = date("d.m.Y",strtotime($row["pr_date_sent"]));
						}else{
							$reminderDate = date("d.m.Y",strtotime($row["pr_date_assigned"]));
						}
						$nalogId = intval($row["prs_nalog_id"]);
						$partnerId = intval($row["prs_partner_id"]);

						$reminderNalogDocumentName = $row['doc_nalog_type'];
						$reminderCandidateDocumentName = $row['doc_candidate_type'];

						if(!is_null($reminderNalogDocumentName)){
							if($languageUser == 0){
								$reminderDocumentName = $row['doc_nalog_type'];
							}
							else{
								$reminderDocumentName = $row['doc_nalog_type_de'];
							}
						}
						else if(!is_null($reminderCandidateDocumentName)){
							if($languageUser == 0){
								$reminderDocumentName = $row['doc_candidate_type'];
							}
							else{
								$reminderDocumentName = $row['doc_candidate_type_de'];
							}
						}
						else{
							$reminderDocumentName = NULL;
						}

						$result["count"][] = $count;
						$result["reminderId"][] = $reminderId;
						$result["reminderLevel"][] = $reminderLevel;
						$result["reminderText"][] = $reminderText;
						$result["reminderType"][] = $reminderType;
						$result["candidateId"][] = $candidateId;
						$result["candidateCheck"][] = $candidateCheck;
						$result["candidateFname"][] = $candidateFname;
						$result["candidateLname"][] = $candidateLname;
						$result["reminderStatus"][] = $reminderStatus;
						$result["reminderDate"][] = $reminderDate;
						$result["nalogId"][] = $nalogId;
						$result["partnerId"][] = $partnerId;
						$result["documentName"][] = $reminderDocumentName;
						
						$count++;
					}
					return $result;
				}else{
					return $result;
				}
			}else{
				return $result;
			}
		}else{
			return $result;
		}
		unset($result);
		unset($puaIdsExp);
		unset($puaIdsConditionArray);
	}
	//REMINDER FUNCTION END 
	function checkContractSentArrayR($canKey){
		Global $db; 
		$result = array();
		if($canKey != ""){
			$canId = intval(getCandidateIdByKey($canKey));
			$nalogId = getNalogForCandidatR($canId);
			$partnerId = getPartnerForCandidateR($canId);
			if($canId != 0 AND $nalogId != 0 AND $partnerId != 0){
				$queryCheck = $db->prepare("
					SELECT 
						id_cs,
						cs_tracking_code,
						cs_tracking_link, 
						cs_sent_date
					FROM 
						idk_pp_contract_sent
					WHERE 
						cs_kandidat_id = :cs_kandidat_id
						AND 
						cs_nalog_id = :cs_nalog_id
						AND 
						cs_partner_id = :cs_partner_id
						AND 
						cs_status != 3
				");
				$queryCheck->execute(array(
					':cs_kandidat_id' => $canId,
					':cs_nalog_id' => $nalogId,
					':cs_partner_id' => $partnerId
				));
				if($queryCheck->rowCount() == 1){
					$rowCheck = $queryCheck->fetch();
					$id_cs = intval($rowCheck["id_cs"]);
					$cs_tracking_code = $rowCheck["cs_tracking_code"];
					$cs_tracking_link = $rowCheck["cs_tracking_link"];
					$cs_sent_date = $rowCheck["cs_sent_date"];
					$result["id_cs"]			= $id_cs;
					$result["cs_tracking_code"] = $cs_tracking_code;
					$result["cs_tracking_link"] = $cs_tracking_link;
					$result["cs_sent_date"]		= $cs_sent_date;

					return $result;
				}else{
					return $result;
				}
			}else{
				return $result;
			}
		}else{
			return $result;
		}
	}
	
	function insertContractSentR($canKey, $trackingCode, $linkTrackingCode, $sentDate){
		Global $userId;
		Global $db;
		/* 
			Response:
				1 		-	izvršen insert
				100		-	nije proslijeđen neki od parametara funkciji
				101		-	kandidat nema nalogId ili partnerId ili nije pronađen candidateId
				102		- 	nema puaId za usera koji je poslao request
		*/
		if($trackingCode != "" AND $linkTrackingCode != "" AND $sentDate != ""){
			$canId = intval(getCandidateIdByKey($canKey));
			$nalogId = getNalogForCandidatR($canId);
			$partnerId = getPartnerForCandidateR($canId);
			if($canId != 0 AND $nalogId != 0 AND $partnerId != 0){
				$puaId = getPuaIdForUser(2, $nalogId, $partnerId);
				if($puaId != 0){
					$queryInsert = $db->prepare("
						INSERT INTO idk_pp_contract_sent
							(
								cs_kandidat_id, 
								cs_nalog_id, 
								cs_partner_id,
								cs_tracking_code,
								cs_tracking_link,
								cs_sent_date,
								cs_status,
								cs_entry_date,
								cs_entry_pua_id
							)
						VALUES
							(
								:cs_kandidat_id, 
								:cs_nalog_id, 
								:cs_partner_id,
								:cs_tracking_code,
								:cs_tracking_link,
								:cs_sent_date,
								:cs_status,
								:cs_entry_date,
								:cs_entry_pua_id
							)
					");
					$queryInsert->execute(array(
						':cs_kandidat_id' => $canId,
						':cs_nalog_id' => $nalogId,
						':cs_partner_id' => $partnerId,
						':cs_tracking_code' => $trackingCode,
						':cs_tracking_link' => $linkTrackingCode,
						':cs_sent_date' => $sentDate,
						':cs_status' => 1,
						':cs_entry_date' => date("Y-m-d H:i:s"),
						':cs_entry_pua_id' => $puaId
					));

					$logDesc = "JobStep PP APP - User PuaId = [".$puaId."] je označio slanje ugovora sa informacijama: Tracking code = ".$trackingCode."; Link = ".$linkTrackingCode."; Datum slanja: ".$sentDate.";";
					addToLogs($logDesc);

					return 1; //1	-	izvršen insert
				}else{
					return 102; //102	- 	nema puaId za usera koji je poslao request
				}
			}else{
				return 101; //101	-	kandidat nema nalogId ili partnerId ili nije pronađen candidateId
			}
		}else{
			return 100; //100	-	nije proslijeđen neki od parametara funkciji
		}
	}

	function getDaysCountR($date){
		Global $db;
		$dateSql = date("Y-m-d", strtotime($date));

		$query = $db->prepare("
			SELECT 
				DATEDIFF
				(
					CURDATE(), 
					DATE_FORMAT(:dateInput, '%Y-%m-%d')
				) 
			AS dateCount
		");
		$query->execute(array(
			":dateInput" => $dateSql
		));
		$row = $query->fetch();
		return intval($row["dateCount"]);
	}

	function getDocumentStatusColorR($vr){
		$vr = intval($vr);

		switch($vr){
			case 3:
				return 'style="background-color: #827d7e; color: white; font-style: italic;"';//Siva
			break;
			case 6:
				return 'style="background-color: #a31880; color: white; font-style: italic;"';//Pinki
			break;
			case 9:
				return 'style="background-color: #5d18a3; color: white; font-style: italic;"';//Ljubičasta
			break;
			case 12:
				return 'style="background-color: #1854a3; color: white; font-style: italic;"';//Plava
			break;
			case 15:
				return 'style="background-color: #18a397; color: black; font-style: italic;"';//svijetlo plava
			break;
			case 18:
				return 'style="background-color: #18a33f; color: white; font-style: italic;"';//Zelena
			break;
			case 21:
				return 'style="background-color: #ff0000; color: white; font-style: italic;"';//Zlatna
			break;
			case 24:
				return 'style="background-color: #a35d18; color: black; font-style: italic;"';//narandzasta
			break;
			case 27:
				return 'style="background-color: #a31818; color: white; font-style: italic;"'; //crvena
			break;
			default:
				return 'style="background-color: #ffffff; color: black; font-style: italic;"'; //bijela
			break; 
		}
	}

	function getActiveVisaIncompleteR($candId, $typeVi, $nalogId){
		Global $db; 
		$statusSql = 0;
		$candidateId = intval($candId);
		$typeVisaIncomplete = intval($typeVi);
		if($typeVisaIncomplete == 1){
			$statusSql = 21;
		}else{
			$statusSql = 24;
		}
		$nalogId = intval($nalogId);
		if($candidateId != 0 AND $nalogId != 0 AND ($typeVisaIncomplete == 1 OR $typeVisaIncomplete == 2)){
			$query = $db->prepare("
				SELECT
					vi.vi_id
				FROM 
					idk_pp_visa_incomplete vi
				INNER JOIN 
					idk_kandidati k
				ON 
					vi.vi_cand_id = k.kandidat_id 
				WHERE 
					k.kandidat_nalog_id = :nalogId
					AND 
					vi.vi_nalog_id = :nalogId
					AND 
					k.kandidat_status_prijave = :statusPrijave
					AND 
					vi.vi_cand_id = :candidateId
					AND 
					vi.vi_type = :typeVi
					AND
					vi.vi_status = 1
			");
			$query->execute(array(
				':nalogId' => $nalogId,
				':statusPrijave' => $statusSql,
				':candidateId' => $candidateId,
				':typeVi' => $typeVisaIncomplete
			));
			if($query->rowCount() == 1){
				$row = $query->fetch();
				return intval($row["vi_id"]);
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}

	function getDetailsForVisaIncompleteArrayR($viId){
		Global $db;
		$result = array(
			"count" =>array(),
			"vi_id" => array(),
			"vi_cand_id" => array(),
			"vi_type" => array(),
			"vi_complaint" => array(),
			"vi_status" => array(),
			"vi_date_received_candidate" => array(),
			"vi_date_we_received_candidate" => array(),
			"vi_deadline_date_candidate" => array(),
			"vi_date_received_employer" => array(),
			"vi_date_we_received_employer" => array(),
			"vi_deadline_date_employer" => array(),
			"vi_nalog_id" => array(),
			"vi_pp_partner_id" => array()
		);
	
		$visaIncompleteId = intval($viId);
	
		if($visaIncompleteId != 0){
			$query = $db->prepare("
				SELECT 
					*
				FROM 
					idk_pp_visa_incomplete
				WHERE 
					vi_id = :vi_id
			");
			$query->execute(array(
				':vi_id' => $visaIncompleteId
			));
			if($query->rowCount() == 1){
				$row = $query->fetch();
				array_push($result["count"], 0);
				array_push($result["vi_id"], $row["vi_id"]);
				array_push($result["vi_cand_id"], $row["vi_cand_id"]);
				array_push($result["vi_type"], $row["vi_type"]);
				array_push($result["vi_complaint"], $row["vi_complaint"]);
				array_push($result["vi_status"], $row["vi_status"]);
				array_push($result["vi_date_received_candidate"], $row["vi_date_received_candidate"]);
				array_push($result["vi_date_we_received_candidate"], $row["vi_date_we_received_candidate"]);
				array_push($result["vi_deadline_date_candidate"], $row["vi_deadline_date_candidate"]);
				array_push($result["vi_date_received_employer"], $row["vi_date_received_employer"]);
				array_push($result["vi_date_we_received_employer"], $row["vi_date_we_received_employer"]);
				array_push($result["vi_deadline_date_employer"], $row["vi_deadline_date_employer"]);
				array_push($result["vi_nalog_id"], $row["vi_nalog_id"]);
				array_push($result["vi_pp_partner_id"], $row["vi_pp_partner_id"]);
			}
		}
	
		return $result;
	}

	function getCountDocumentsVisaIncomplete($canId, $nalogId, $viId){
		Global $db;
		$canId = intval($canId);
		$nalogId = intval($nalogId);
		$viId = intval($viId);

		if($canId != 0 AND $nalogId != 0 AND $viId != 0){
			$query = $db->prepare("
				SELECT
					count(crd_id) AS countDoc
				FROM 
					idk_pp_cand_required_documents
				WHERE 
					crd_cand_id = :crd_cand_id
					AND 
					crd_nalog_id = :crd_nalog_id
					AND 
					crd_done_by = 1
					AND
					crd_status = 1
					AND 
					crd_vi_id = :crd_vi_id
			");
			$query->execute(array(
				":crd_cand_id" => $canId,
				":crd_nalog_id" => $nalogId,
				":crd_vi_id" => $viId
			));
			$row = $query->fetch();

			return intval($row["countDoc"]);

		}else{
			return 0;
		}
	}

	function getCountDocuments($nalogId, $candidateId){
		Global $db;
		$nalogId = intval($nalogId);
		$candidateId = intval($candidateId);
		if($nalogId != 0 AND $candidateId != 0){

			$nacinOdlaska = getCandidateNacinOdlaska($candidateId);
			$potpunaNostrifikacija = getCandidateFullRecognition($candidateId);
			$uslov = "";
			if ( $nacinOdlaska == 2 OR $potpunaNostrifikacija == 1 OR $potpunaNostrifikacija == 2) {
				$uslov = "
					AND 
					nrd_west_balkan = 1
				";
			} else if ( $nacinOdlaska == 3 ) { 
				$uslov = "
					AND 
					nrd_work_experience = 1
				";
			} else if ( $nacinOdlaska == 0 ) { 
				$uslov = "
					AND 
					nrd_skilled_candidates = 1
				";
			}
			

			$query = $db->prepare("
				SELECT
					count(nrd_id) AS countDoc
				FROM 
					idk_pp_nalog_required_documents
				WHERE  
					nrd_nalog_id = :nrd_nalog_id
					AND 
					nrd_done_by = 1
					AND
					nrd_status = 1
					".$uslov."
			");
			$query->execute(array(
				":nrd_nalog_id" => $nalogId
			));
			$row = $query->fetch();

			return intval($row["countDoc"]);

		}else{
			return 0;
		}
	}

	function getNostrificationDocument($idCan){
		Global $db; 
		$idCan = intval($idCan);
		$result = array(
			"count" => array(),
			"file" => array(),
			"date" => array(),
			"employee" => array()
		);
		if($idCan != 0){
			$query = $db->prepare("
				SELECT 
					file_nd, upload_date_nd, upload_employee_id
				FROM 
					idk_nostrifikovane_diplome
				WHERE 
					id_cand_job = :id_cand_job
			");
			$query->execute(array(
				":id_cand_job" => $idCan
			));
			if($query->rowCount() == 1){
				$row = $query->fetch();
				$file = $row["file_nd"];
				$date = date("d.m.Y H:i", strtotime($row["upload_date_nd"]));
				$employee = getEmployeeFullNameById($row["upload_employee_id"]);
				array_push($result["count"], 0);
				array_push($result["file"], $file);
				array_push($result["date"], $date);
				array_push($result["employee"], $employee);

				return $result;
			}else{
				return $result;
			}
		}else{
			return $result;
		}
	}
	//Other Appointments Of Candidates START 

	function getOtherAppointmentsOfCandidateArrayR($candidatId, $nalogId){
		Global $db;
		Global $userId;

		$arrayResult = array(
			"count" => array(),
			"pap_id" => array(),
			"pap_date" => array(),
			"pap_city" => array(),
			"pap_nalog_id" => array(),
			"pca_id" => array(),
			"pca_time" => array(),
			"pca_avg_rating" => array()
		);

		$candidatId = intval($candidatId);
		$nalogId = intval($nalogId);
		if($candidatId != 0 AND $nalogId != 0){
			//Na osnovu parametra candidatId potrebno je provjeriti u kojem se nalogu trenutno kandidat nalazi
			//U slucaju da kandidat nije ni u jednom nalogu, funkcija ce vratiti vrijednost 0
			//U tom slucaju uzima se proslijeđeni parametar nalogIdd
			//Zasto to tako radimo
			//Na listi kandidata "Odbijeni" - kandidat ostaje vidljim poslodavcu
			//Prilikom pregleda profila, sa liste "Odbijeni" get-a se parametar nalog
			//Da nije ovog načina - funkcija bi vidjela da kandidat nije ni u jednom nalogu i ne bi se izvrsio ostatak kod-a
			//Ovako mozemo cak i ako kandidat nije u tom nalogu vise tj. ako je nekad bio na nekom nalogu odbijen, vidjeti ostale podatke gdje je ovaj parametar pocetni
			/*$nalogId = getNalogForCandidatR($candidatId); 
			if($nalogId == 0){
				$nalogId = $nalogIdd;
			}*/
			//Kada imamo nalogId - onda od tog naloga trazimo kompaniju
			$companyId = getCompanyIdFromNalogR($nalogId);
			if($companyId != "Undefined" AND $companyId != 0){
				//Kada imamo companyId onda mozemo pogledati sve naloge od te kompanije
				$nalogIds = getAktivniNaloziKompanijeArrayIdR($companyId);
				$nalogIdsImp = implode(",", $nalogIds);
				if(count($nalogIds) != 0){
					
					$query = $db->prepare("
					SELECT 
						pap.pap_id, pap.pap_date, pap.pap_city, pap.pap_nalog_id, pca.pca_id, pca.pca_time, pca.pca_avg_rating
					FROM 
						idk_pp_appointments pap
					INNER JOIN
						idk_pp_cand_appts pca
					ON 
						pap.pap_id = pca.pca_appointment_id
					WHERE 
						pap.pap_nalog_id IN (".$nalogIdsImp.") 
						AND 
						pap.pap_nalog_id != ".$nalogId."
						AND 
						pca.pca_kandidat_id = :pca_kandidat_id
					");
					$query->execute(array(
						':pca_kandidat_id' => $candidatId
					));
					if($query->rowCount() != 0){
						$cnt = 0;
						while($row = $query->fetch()){
							$pap_id = intval($row["pap_id"]);
							$pap_date = date("d.m.Y", strtotime($row["pap_date"]));
							$pap_city = $row["pap_city"];
							$pap_nalog_id = $row["pap_nalog_id"];
							$pca_id = intval($row["pca_id"]);
							$pca_time = date("H:i", strtotime($row["pca_time"]));
							$pca_avg_rating = $row["pca_avg_rating"];
							if($pca_avg_rating != NULL){
								$pca_avg_rating = $pca_avg_rating;
							}else{
								$pca_avg_rating = "noRating";
							}
							array_push($arrayResult["count"], $cnt);
							array_push($arrayResult["pap_id"], $pap_id);
							array_push($arrayResult["pap_date"], $pap_date);
							array_push($arrayResult["pap_city"], $pap_city);
							array_push($arrayResult["pap_nalog_id"], $pap_nalog_id);
							array_push($arrayResult["pca_id"], $pca_id);
							array_push($arrayResult["pca_time"], $pca_time);
							array_push($arrayResult["pca_avg_rating"], $pca_avg_rating);
							
							$cnt++;
						}
						return $arrayResult;
					}else{
						return $arrayResult;
					}
				}else{
					return $arrayResult; 
				}

			}else{
				return $arrayResult;
			}
		}else{
			return $arrayResult;
		}
		unset($arrayResult);
	}

	//Other Appointments Of Candidates END

	function getNalogForAccess($canId, $nalogUrl){
		Global $db; 
		/*	
		*********************************************************************************************************************************
		*********************************************************************************************************************************
			REZULTAT:
				Slučaj 1
					Trenutni nalog kandidata == 0 												-> 	Rezultat: Flag = 1 Nalog = nalogURL
				***********************************************************************************************************************
				SLUČAJ 2
					Trenutni nalog kandidata != 0 && Nalog iz linka se nalazi u nalogIds 		-> 	Rezultat: Flag = 0 Nalog = nalogId
				***********************************************************************************************************************
				SLUČAJ 3
					Trenutni nalog kandidata != 0 && Nalog iz linka se ne nalazi u nalogIds 	-> 	Rezultat: Flag = 1 Nalog = nalogURL
				***********************************************************************************************************************		
		*********************************************************************************************************************************
		*********************************************************************************************************************************		
		*/
		$result = array(
			"flag" => array(),
			"nalogId" => array(),
		);

		$canId = intval($canId);
		$nalogUrl = intval($nalogUrl);

		if($canId != 0 AND $nalogUrl != 0){

			$nalogId = getNalogForCandidatR($canId);

			if($nalogId != 0){
				$companyId = getCompanyIdFromNalogR($nalogId);
				$nalogIds = getAktivniNaloziKompanijeArrayIdR($companyId);

				if(in_array($nalogUrl, $nalogIds)){
					//Slučaj 2
					array_push($result["flag"], 0);
					array_push($result["nalogId"], $nalogId);
				}else{
					//Slučaj 3
					array_push($result["flag"], 1);
					array_push($result["nalogId"], $nalogUrl);
				}
			}else{
				//Slucaj 1
				array_push($result["flag"], 1);
				array_push($result["nalogId"], $nalogUrl);
			}
		}else{
			array_push($result["flag"], "undefined");
			array_push($result["nalogId"], "undefined");
		}
		return $result;

		unset($result);
	}

	function getDIPLStatusOutput($status_id, $naziv_ustanove = NULL){
		Global $txtArray;
		Global $userId;
		$languageUser = getLanguageForUser($userId);

		$cell_text_status = "";
		// var_dump($txtArray['Poslan zahtjev']);
		if ($status_id == 1 || is_null($status_id) || $status_id == 7){
			$cell_text_status = '<div style = "text-align:center;margin:auto;" class = "nostrification_status nostrification_lead_in_process">'.$txtArray['U obradi Lead'][$languageUser].'</div>';
		}
		else if($status_id == 2){
			$cell_text_status = '<div style = "text-align:center;margin:auto;" class = "nostrification_status nostrification_collecting_documents">'.$txtArray['Prikupljanje dokumentacije'][$languageUser].'</div>';
		}
		else if($status_id == 3){
			$cell_text_status = '<div style = "text-align:center;margin:auto;" class = "nostrification_status nostrification_request_sent">'.$txtArray['Poslan zahtjev'][$languageUser].'</div>';
		}
		else if($status_id == 4){
			$cell_text_status = '<div style = "text-align:center;margin:auto;" class = "nostrification_status nostrification_in_process">'.$txtArray['U obradi na'][$languageUser].' <i><u>'.$naziv_ustanove.'</u></i></div>';
		}
		else if($status_id == 5){
			$cell_text_status = '<div style = "text-align:center;margin:auto;" class = "nostrification_status nostrification_supplementing_documents">'.$txtArray['Dopuna dokumentacije'][$languageUser].'</div>';
		}
		else if($status_id == 6){
			$cell_text_status = '<div style = "text-align:center;margin:auto;" class = "nostrification_status nostrification_finished">'.$txtArray['Završeno'][$languageUser].'</div>';
		}
		return $cell_text_status;
	}

	function getLanguageLevelOutput($meets_criteria, $candidate_language_level, $candidate_language_status){
		$class_to_append = "";
		
		if($meets_criteria){
			$class_to_append = "language_green";
		}
		else{
			$class_to_append = "language_orange";
		}

		// var_dump($candidate_language_level);
		// exit();
		if($candidate_language_level == '0' OR is_null($candidate_language_level)){
			$candidate_language_level = "BZ";
		}

		if($candidate_language_status == 3){
			$candidate_language_level .= ".1";
		}
		else if($candidate_language_status == 4){
			$candidate_language_level .= ".2";
		}
		
		$cell_text = '<div style = "margin:auto;" class = "language_status '.$class_to_append.'">'.$candidate_language_level.'</div>';

		return $cell_text;
	}

	function checkIfLanguageMeetsCriteria($required_language_level, $candidate_language_level){
		Global $db;
		
		$return_value = 0;
		
		if($required_language_level == 'BZ')
			$return_value = 1;
		else if($required_language_level == 'A1' AND strpos('C2 C1 B2 B1 A2 A1', $candidate_language_level))
			$return_value =  1;
		else if($required_language_level == 'A2' AND strpos('C2 C1 B2 B1 A2', $candidate_language_level))
			$return_value =  1;
		else if($required_language_level == 'B1' AND strpos('C2 C1 B2 B1', $candidate_language_level))
			$return_value =  1;
		else if($required_language_level == 'B2' AND strpos('C2 C1 B2', $candidate_language_level))
			$return_value =  1;
		else if($required_language_level == 'C1' AND strpos('C2 C1', $candidate_language_level))
			$return_value =  1;
		else if($required_language_level == 'C2' AND strpos('C2', $candidate_language_level))
			$return_value =  1;

		return $return_value;
	}

	function getLanguageLevelStatusOutput($candidate_language_status, $candidate_language_exam_date, $candidate_language_certificate_expiration_date){
		Global $txtArray;
		Global $userId;
		$languageUser = getLanguageForUser($userId);
		$cell_text = "";
		// var_dump(is_null($candidate_language_status));
		if($candidate_language_status == 1 OR is_null($candidate_language_status)){
			$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_red">'.$txtArray['Samoprocjena'][$languageUser].'</div>';
		}
		else if($candidate_language_status == 2){
			$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_orange">'.$txtArray['Sam uči'][$languageUser].'</div>';
		}
		else if($candidate_language_status == 3){
			$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_yellow">'.$txtArray['Pohađa podnivo 1'][$languageUser].'</div>';
		}
		else if($candidate_language_status == 4){
			$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_yellow">'.$txtArray['Pohađa podnivo 2'][$languageUser].'</div>';
		}
		else if($candidate_language_status == 5){
			$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_blue">'.$txtArray['Čeka datum polaganja'][$languageUser].'</div>';
		}
		else if($candidate_language_status == 6){
			if(is_null($candidate_language_exam_date)){
				$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_blue">'.$txtArray['Čeka polaganje'][$languageUser].'</div>';
			}
			else{
				$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_blue">'.$txtArray['Čeka polaganje'][$languageUser].' ('.date("d.m.Y", strtotime($candidate_language_exam_date)).')</div>';
			}
		}
		else if($candidate_language_status == 7){
			$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_purple">'.$txtArray['Čeka razultat'][$languageUser].'</div>';
		}
		else if($candidate_language_status == 8){
			if(is_null($candidate_language_certificate_expiration_date)){
				$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_green">'.$txtArray['Ima certifikat'][$languageUser].'</div>';
			}
			else{
				$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_green">'.$txtArray['Ima certifikat'][$languageUser].' ('.date("d.m.Y", strtotime($candidate_language_certificate_expiration_date)).')</div>';
			}
		}
		else if($candidate_language_status == 9){
			$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_purple">'.$txtArray['Certifikat istekao'][$languageUser].'</div>';
		}
		else if($candidate_language_status == 10){
			$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_green">'.$txtArray['Napreduje na veći nivo'][$languageUser].'</div>';
		}
		else if($candidate_language_status == 11){
			$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_orange">'.$txtArray['Nije položio'][$languageUser].'</div>';
		}
		else if($candidate_language_status == 12){
			$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_red">'.$txtArray['Odustao'][$languageUser].'</div>';
		}
		else if($candidate_language_status == 13){
			$cell_text = '<div style = "text-align:center;margin:auto;" class = "language_status language_red">'.$txtArray['Arhiva'][$languageUser].'</div>';
		}
		
		return $cell_text;
	}
	function getStatusPrijaveOutput($candidate_status_prijave){
		
		Global $txtArray;
		Global $userId;
		$languageUser = getLanguageForUser($userId);

		$cell_text = "";
		if($candidate_status_prijave == 3){
			$cell_text = '<div class = "status_prijave status_prijave_interview"><b>'.$txtArray['Intervju'][$languageUser].'</b></div>';
		}
		else if($candidate_status_prijave == 4){
			$cell_text = '<div class = "status_prijave status_prijave_hired"><b>'.$txtArray['Zaposlen'][$languageUser].'</b></div>';
		}
		else if($candidate_status_prijave == 5){
			$cell_text = '<div class = "status_prijave status_prijave_rejected"><b>'.$txtArray['Odbijen'][$languageUser].'</b></div>';
		}
		else if($candidate_status_prijave == 7){
			$cell_text = '<div class = "status_prijave status_prijave_waiting_contract"><b>'.$txtArray['Čeka ugovor'][$languageUser].'</b></div>';
		}
		else if($candidate_status_prijave == 8){
			$cell_text = '<div class = "status_prijave status_prijave_contract_sent"><b>'.$txtArray['Poslan ugovor'][$languageUser].'</b></div>';
		}
		else if($candidate_status_prijave == 9){
			$cell_text = '<div class = "status_prijave status_prijave_contract_signed"><b>'.$txtArray['Potpisan ugovor'][$languageUser].'</b></div>';
		}
		else if($candidate_status_prijave == 10){
			$cell_text = '<div class = "status_prijave status_prijave_started_working"><b>'.$txtArray['Početak rada'][$languageUser].'</b></div>';
		}
		else if($candidate_status_prijave == 12){
				$cell_text = '<div class = "status_prijave status_prijave_collecting_documents"><b>'.$txtArray['Skuplja dokumentaciju'][$languageUser].'</b></div>';
		}
		else if($candidate_status_prijave == 15){
			$cell_text = '<div class = "status_prijave status_prijave_waiting_appointment"><b>'.$txtArray['Čeka termin'][$languageUser].'</b></div>';
		}
		else if($candidate_status_prijave == 18){
			$cell_text = '<div class = "status_prijave status_prijave_waiting_visa"><b>'.$txtArray['Čeka vizu'][$languageUser].'</b></div>';
		}
		else if($candidate_status_prijave == 27){
			$cell_text = '<div class = "status_prijave status_prijave_got_visa"><b>'.$txtArray['Dobio vizu'][$languageUser].'</b></div>';
		}
		else if($candidate_status_prijave == 21){
			$cell_text = '<div class = "status_prijave status_prijave_document_addition"><b>'.$txtArray['Dopuna dokumentacije'][$languageUser].'</b></div>';
		}
		else if($candidate_status_prijave == 24){
			$cell_text = '<div class = "status_prijave status_prijave_visa_rejected"><b>'.$txtArray['Odbijena viza'][$languageUser].'</div>';
		}
		
		return $cell_text;
	}

	function getCompanyNameByUserId(){
		Global $userId;
		Global $db;
		
		$query_get_company_name = $db -> prepare('
			SELECT company_name 
			FROM idk_companies c
			JOIN idk_pp_users u
			ON u.pu_company_id = c.company_id
			WHERE u.pu_id = :user_id
		');
		$query_get_company_name -> execute(array(':user_id' => $userId));
		$row_get_company_name = $query_get_company_name -> fetch();
		return $row_get_company_name['company_name'];

	}
	
    function candidateHasProjection($candidate_id){
		Global $db;
		
		$query_get_candidate = $db -> prepare('
			SELECT kandidat_id
			FROM idk_kandidat_projekcije
			WHERE kandidat_id = :candidate_id
		');

		$query_get_candidate -> execute(array(':candidate_id' => $candidate_id));

		$row_get_candidate = $query_get_candidate -> fetch();

		if(is_null($row_get_candidate['kandidat_id'])){
			return 0;
		}
		return 1;
	}


	function getCandidateStatusPrijaveByCandidateId($candidate_id){
		Global $db;

		$query_get_status = $db -> prepare('
			SELECT kandidat_status_prijave
			FROM idk_kandidati
			WHERE kandidat_id = :candidate_id
		');
		$query_get_status -> execute(array(':candidate_id' => $candidate_id));

		$row_get_status = $query_get_status -> fetch();

		return $row_get_status['kandidat_status_prijave'];
	}

	function deleteCandidatesProjection($candidate_id){
		Global $db;

		$query_delete_candidate_projection = $db -> prepare('
			DELETE FROM idk_kandidat_projekcije
			WHERE kandidat_id = :candidate_id
		');

		$query_delete_candidate_projection -> execute(array(':candidate_id' => $candidate_id));
	}

	function getCandidatesQuickAccessProjection($candidate_id){
		Global $db;

		$query_get_projection = $db -> prepare('
			SELECT proracunati_pocetak_rada
			FROM idk_kandidat_projekcije
			WHERE kandidat_id = :candidate_id
		');

		$query_get_projection -> execute(array(':candidate_id' => $candidate_id));

		$row_get_projection = $query_get_projection -> fetch();

		return strtotime($row_get_projection['proracunati_pocetak_rada']);
	}

	function calculateCandidatesProjection($candidate_id){
		include_once('includes/classes/candidatesProjection.php');
		$durationPerStatus = new durationPerStatus();

		$candidates_projection = new candidatesProjection($durationPerStatus, $candidate_id);

        $projection_object       = $candidates_projection -> getCandidateProjectionRows();
		return $projection_object[0]['candidate_assessment'];
	}

	function updateCandidatesQuickAccessProjection($candidate_id, $projection_date){
		Global $db;

		$query_update_projection =  $db -> prepare('
			UPDATE idk_kandidat_projekcije
			SET proracunati_pocetak_rada =  :projection_date
			WHERE kandidat_id = :candidate_id
		');

		$query_update_projection -> execute(array(
			':candidate_id' => $candidate_id,
			':projection_date' => $projection_date
		));
	}

	function insertCandidateQuickAccessProjection($candidate_id, $projection_date){
		Global $db;
		$query_insert_projection = $db -> prepare('
			INSERT INTO idk_kandidat_projekcije (kandidat_id, proracunati_pocetak_rada)
			VALUES (:candidate_id, :projection_date)
		');
		$query_insert_projection -> execute(array(
			':candidate_id' => $candidate_id,
			':projection_date' => $projection_date
		));
	}

	function updateCandidateInstallments($candidate_id, $shift_dates){
		Global $db;

		$query_update_instalments = $db -> prepare('
			UPDATE idk_kandidat_financije
			SET kf_datum = DATE_ADD(kf_datum, INTERVAL :shift_dates DAY), kf_datum_stvarni = DATE_ADD(kf_datum_stvarni, INTERVAL :shift_dates DAY)
			WHERE kandidat_id = :candidate_id
            AND kf_placeno = 0
            AND kf_type != 0
		');

		$query_update_instalments -> execute(array(
			':candidate_id' => $candidate_id,
			':shift_dates' => $shift_dates
		));
	}

	function updateCandidateProjectionAndInstallment($candidate_id){

		$shift_dates = 0;

		$candidate_status = getCandidateStatusPrijaveByCandidateId($candidate_id);
		$candidate_has_projection = candidateHasProjection($candidate_id);

		$negative_statuses = array(1,2,3,5,6);
		// $positive_statuses = array(4,7,8,9,10,12,15,18,21,24,27);

		if(in_array($candidate_status, $negative_statuses)){
			if($candidate_has_projection){
				deleteCandidatesProjection($candidate_id);
                //KANDIDAT BIO NA MINIMALNO STATUSU ČEKA UGOVOR, POTREBAN KOD ZA ZAMJENU
			}
			return;
		}
		else{
			$candidate_calculated_projection  = calculateCandidatesProjection($candidate_id);

			if($candidate_has_projection){

				$candidate_quick_access_projection = getCandidatesQuickAccessProjection($candidate_id);				
				if($candidate_quick_access_projection != $candidate_calculated_projection){
					$shift_dates = floor(($candidate_quick_access_projection - $candidate_calculated_projection)/86400);
					updateCandidatesQuickAccessProjection($candidate_id, date('Y-m-d', $candidate_calculated_projection));
				}
				else return;
			}
			else{
				insertCandidateQuickAccessProjection($candidate_id, date('Y-m-d', $candidate_calculated_projection));
			}
		}

		if($shift_dates != 0){
			updateCandidateInstallments($candidate_id, $shift_dates);
		}
	}

	
	function getRegionNameByIdR($regija_id){
		Global $db;

		$query = $db -> prepare('
			SELECT pr_name
			FROM idk_pp_regions
			WHERE pr_id  = :pr_id 
		');

		$query -> execute(array(
			':pr_id' => $regija_id 
		));

		$row = $query->fetch();

		return $row["pr_name"];
	}

	function getCityNameByIdR($grad_id){
		Global $db;

		$query = $db -> prepare('
			SELECT pc_name
			FROM idk_pp_city
			WHERE pc_id  = :pc_id 
		');

		$query -> execute(array(
			':pc_id' => $grad_id 
		));

		$row = $query->fetch();

		return $row["pc_name"];
	}

	function getDIPLStatusByCandId($id_broj_nd_kandidata){
		Global $db;

		$query = $db -> prepare('
			SELECT status_nd_kandidata
			FROM idk_nd_kandidata
			WHERE id_broj_nd_kandidata  = :id_broj_nd_kandidata 
		');

		$query -> execute(array(
			':id_broj_nd_kandidata' => $id_broj_nd_kandidata 
		));

		$row = $query->fetch();

		return $row["status_nd_kandidata"];
	}

	function getDIPLInstitutionName($id_broj_nd_kandidata){
		Global $db;

		$query = $db -> prepare('
			SELECT naziv_ustanove_nd
			FROM idk_nd_kandidata
			INNER JOIN idk_nd_ustanove ON idk_nd_kandidata.idd_ustanova_nd = idk_nd_ustanove.id_ustanove_nd
			WHERE id_broj_nd_kandidata  = :id_broj_nd_kandidata 
		');

		$query -> execute(array(
			':id_broj_nd_kandidata' => $id_broj_nd_kandidata 
		));

		$row = $query->fetch();

		return $row["naziv_ustanove_nd"];
	}

	function getUserPPIdFromKey($user_key){
		Global $db;

		$query = $db->prepare("
			SELECT pu_id
			FROM idk_pp_users
			WHERE pu_key = :pu_key
		");
		$query->execute(array(
			':pu_key' => $user_key
		));
		$row = $query->fetch();
		$pu_id = $row["pu_id"];
		return $pu_id;
	}

	function getUserPuKeyFromEmail($user_email){
		Global $db;

		$query = $db->prepare("
			SELECT pu_key
			FROM idk_pp_users
			WHERE pu_email = :pu_email
		");
		$query->execute(array(
			':pu_email' => $user_email
		));
		$row = $query->fetch();
		$pu_key = $row["pu_key"];
		return $pu_key;
	}
	function getLanguageForUserByToken($pu_key){
		Global $db;
		$query = $db->prepare("
			SELECT
				pu_language
			FROM 
				idk_pp_users
			WHERE 
				pu_key = :pu_key
		");
		$query->execute(array(
			':pu_key' => $pu_key
		));
		$row = $query->fetch();
		$language = intval($row["pu_language"]);
		
		return $language;
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
