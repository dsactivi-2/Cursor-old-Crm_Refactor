<?php 
	include("includes/function.php");
	
	//$puaId = getPuaIdForUser(1, 225, 0);
	
	//$fff = getAccessControlArrayR($idKandidat, $nalogId, $partnerId, $remiderType);
	
	exit();
	// function getAllPuaIdsForUserArrayR(){
		// Global $db;
		// Global $userId;
		
		// $result = array();
		
		// if($userId != 0){
			// $query = $db->prepare("
				// SELECT 
					// pua_id 
				// FROM 
					// idk_pp_user_access 
				// WHERE 
					// pua_user_id = :pua_user_id
					// AND 
					// pua_status = 1
			// ");
			// $query->execute(array(
				// ':pua_user_id' => $userId
			// ));
			// while($row = $query->fetch()){
				// $puaId = intval($row["pua_id"]);
				// array_push($result, $puaId);
			// }
			// return $result;
		// }else{
			// return $result;
		// }
		
		// unset($result);
	// }
	
	// function getRemindersArrayR(){
		// Global $db;
		// Global $userId;
		
		// $result = array(
			// "count" => array(),
			// "reminderId" => array(),
			// "reminderText" => array(),
			// "reminderType" => array(),
			// "candidateId" => array(),
			// "candidateCheck" => array(),
			// "candidateFname" => array(),
			// "candidateLname" => array(),
			// "reminderStatus" => array(),
			// "reminderDateSent" => array(),
			// "nalogId" => array(),
			// "partnerId" => array(),
		// );
		
		// if($userId != 0){
			// $puaIdsExp = getAllPuaIdsForUserArrayR();
			// $puaIdsImp = implode(",",$puaIdsExp);
			// $puaIdsConditionArray = array();
			// foreach($puaIdsExp AS $puaIdVal){
				// array_push($puaIdsConditionArray, "(pr.pr_users_sent LIKE '".$puaIdVal."' OR pr.pr_users_sent LIKE '".$puaIdVal.",%' OR pr.pr_users_sent LIKE '%,".$puaIdVal.",%' OR pr.pr_users_sent LIKE '%,".$puaIdVal."')");
			// }
			// $puaIdsCondition = implode(" OR ", $puaIdsConditionArray);
			// if(count($puaIdsConditionArray) != 0){
				// $query = $db->prepare("
					// SELECT 
						// pr.pr_id, pr.pr_candidate_id, kan.kandidat_check, kan.kandidat_ime, kan.kandidat_prezime, pr.pr_date_sent, pr.pr_status, prs.prs_nalog_id, prs.prs_partner_id, prs.prs_reminder_type_id, prt.prt_notification_text_de
					// FROM 
						// idk_pp_reminders pr 
					// JOIN 
						// idk_pp_reminder_settings prs
					// ON 
						// pr.pr_reminder_setting_id = prs.prs_id 
					// JOIN 
						// idk_pp_reminder_types prt
					// ON 
						// prs.prs_reminder_type_id = prt.prt_id 
					// JOIN 
						// idk_kandidati kan
					// ON 
						// pr.pr_candidate_id = kan.kandidat_id 
					// WHERE 
						// (
							// pr.pr_status = 1 
							// OR 
							// (
								// pr.pr_status = 2 
								// AND 
								// pr.pr_user_assigned IN (".$puaIdsImp.")
							// )
						// )
						// AND 
						// (
							// ".$puaIdsCondition."
						// )
						// AND 
						// prs.prs_active = 1
				// ");
				// $query->execute();
				// $cntQuery = $query->rowCount();
				// if($cntQuery != 0){
					// $count = 0;
					// while($row = $query->fetch()){
						// $reminderId = intval($row["pr_id"]);
						// $reminderText = $row["prt_notification_text_de"];
						// $reminderType = intval($row["prs_reminder_type_id"]);
						// $candidateId = intval($row["pr_candidate_id"]);
						// $candidateCheck = $row["kandidat_check"];
						// $candidateFname = $row["kandidat_ime"];
						// $candidateLname = $row["kandidat_prezime"];
						// $reminderStatus = intval($row["pr_status"]);
						// $reminderDateSent = date("d.m.Y",strtotime($row["pr_date_sent"]));
						// $nalogId = intval($row["prs_nalog_id"]);
						// $partnerId = intval($row["prs_partner_id"]);
						
						// $result["count"][] = $count;
						// $result["reminderId"][] = $reminderId;
						// $result["reminderText"][] = $reminderText;
						// $result["reminderType"][] = $reminderType;
						// $result["candidateId"][] = $candidateId;
						// $result["candidateCheck"][] = $candidateCheck;
						// $result["candidateFname"][] = $candidateFname;
						// $result["candidateLname"][] = $candidateLname;
						// $result["reminderStatus"][] = $reminderStatus;
						// $result["reminderDateSent"][] = $reminderDateSent;
						// $result["nalogId"][] = $nalogId;
						// $result["partnerId"][] = $partnerId;
						
						// $count++;
					// }
					// return $result;
				// }else{
					// return $result;
				// }
			// }else{
				// return $result;
			// }
		// }else{
			// return $result;
		// }
		// unset($result);
		// unset($puaIdsExp);
		// unset($puaIdsConditionArray);
	// }
	
	// $result = getRemindersArrayR();
	
	// print_r($result); 
	
	// unset($result);
	
	// $puaIdsExp = getAllPuaIdsForUserArrayR();
	// $puaIdsImp = implode(",",$puaIdsExp);
	// $puaIdsConditionArray = array();
	// foreach($puaIdsExp AS $puaIdVal){
		// array_push($puaIdsConditionArray, "".$puaIdVal." IN (pr.pr_users_sent)");
	// }
	
	// echo "<br>".implode(" OR ", $puaIdsConditionArray);
	// $puaIds = getAllPuaIdsForUserArrayR();
	// print_r($puaIds);
	
	// echo "<br>".implode(",",$puaIds)."<br>";
	
?>