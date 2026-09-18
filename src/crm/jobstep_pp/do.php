<?php 
	include("includes/function.php");
	include("includes/language/language.php");
	//Generisati kljuc za registraciju na nacin $str = date("YmdHis").rand().date("siHdmY");
	$isLoggedIn = isLoggedInR();
	$languageUser = getLanguageForUser($userId);
	if($isLoggedIn == 1){
		$page = $_REQUEST["page"];
		switch($page){
			case "logOut":
				//Add to LOGS
				$logDesc = "JobStep PP APP - Zaposlenik se odjavio.";
				$logDate = date("Y-m-d H:i:s");

				$logQuery = $db->prepare("
					INSERT INTO idk_logs
					(log_employeeid, log_desc, log_date, log_type)
					VALUES
					(:log_employeeid, :log_desc, :log_date, :log_type)
				");

				$logQuery->execute(array(
					':log_employeeid' => $userId,
					':log_desc' => $logDesc,
					':log_date' => $logDate,
					':log_type' => 10
				));
				
				setcookie("ppJobStepSession", "", time() - 60 * 60 * 24 * 30);
				unset($_COOKIE["ppJobStepSession"]);
				
				header("Location:".getSiteUrlr()."landing.php");
			break;
			case "tm_get_reminder_types":

				$query_get_reminder_types = $db -> prepare('
					SELECT prt_id, prt_name
					FROM idk_pp_reminder_types
					WHERE prt_user_type IN (1,2)
				');
				$query_get_reminder_types -> execute();
				
				$reminder_types = array();
		// var_dump($txtArray);
		// exit();
				while($row_get_reminder_types = $query_get_reminder_types -> fetch()){
					$count_reminders_by_type = count(getRemindersArrayR($row_get_reminder_types['prt_id'])["count"]);
					array_push($reminder_types, array(
						"reminder_type" => $row_get_reminder_types['prt_id'],
						// "reminder_name" => $row_get_reminder_types['prt_name'],
						"reminder_name" => $txtArray[$row_get_reminder_types['prt_name']][$languageUser],
						"reminder_count" => $count_reminders_by_type,
						"selected"		=> false
					));
				}
				echo json_encode($reminder_types);

			break;

			case "tm_get_candidate_cards":

				$selected_reminders = $_REQUEST['selected_reminders'];
				$candidates_cards_info = getRemindersArrayR($selected_reminders);
				$transposed_candidates_cards_info = array();
				
				for($i = 0; $i < count($candidates_cards_info['count']); $i++){
					array_push($transposed_candidates_cards_info,array(
						"candidateCheck" 	=> $candidates_cards_info['candidateCheck'][$i],
						"candidateFname" 	=> $candidates_cards_info['candidateFname'][$i],
						"candidateLname" 	=> $candidates_cards_info['candidateLname'][$i],
						"reminderStatus" 	=> $candidates_cards_info['reminderStatus'][$i],
						"reminderText" 		=> $candidates_cards_info['reminderText'][$i],
						"reminderType" 		=> $candidates_cards_info['reminderType'][$i],
						"reminderDate" 		=> $candidates_cards_info['reminderDate'][$i],
						"candidateId" 		=> $candidates_cards_info['candidateId'][$i],
						"reminderId"		=> $candidates_cards_info['reminderId'][$i],
						"reminderLevel"		=> $candidates_cards_info['reminderLevel'][$i],
						"partnerId" 		=> $candidates_cards_info['partnerId'][$i],
						"nalogId" 			=> $candidates_cards_info['nalogId'][$i],
						"documentName" 		=> $candidates_cards_info['documentName'][$i],
						"is_selected"		=> false,
					));
				}
				// echo json_encode($candidates_cards_info);
				echo json_encode($transposed_candidates_cards_info);


			break;
			
			case "tm_get_company_name_by_user_id":
				echo json_encode(getCompanyNameByUserId());
			break;
		}
	}else{
		header("Location:".getSiteUrlr()."landing.php");
	}

?>