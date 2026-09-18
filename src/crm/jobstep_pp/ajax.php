<?php
include("includes/function.php");
include("includes/connect.php");
include("includes/classes/candidatesProjection.php");
ini_set('display_errors', 0);
ini_set('error_log', 'error_log');
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$languageUser = getLanguageForUser($userId);
include("includes/language/language.php");
//PHPMailer
require '../mail/Exception.php';
require '../mail/PHPMailer.php';
require '../mail/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$action = $_REQUEST['action'];
switch($action){
	case "check_login_data":
		// case koji na osnovu login email-a password vraća:
		// 0 ako nije ispravan mail
		// 1 ako nije ispravna šifra
		// 2 ako su podaci ok
		// 3 deaktiviran
		
		$logInEmail 		= $_POST['input_email'];
		$logInPassword 	= $_POST['input_password'];
		$logInCheck = $_POST["input_check"];
		if($logInCheck == 1){
			$logInCheck = "on";
		}else{
			$logInCheck = "off";
		}
		
		$logInQuery = $db->prepare("
			SELECT 
				pu_id, 
				pu_email, 
				pu_password, 
				pu_key, 
				pu_status
			FROM idk_pp_users
			WHERE pu_email = :pu_email
		");
		$logInQuery->execute(array(
			':pu_email' => $logInEmail
		));
		if($logInQuery->rowCount() != 0){
			$logInRow = $logInQuery->fetch();
			if(md5($logInPassword) == $logInRow["pu_password"]){
				if($logInRow["pu_status"] != 0){
					if($logInCheck == "on") {
						$month = time() + 60 * 60 * 24 * 30;
						setcookie("ppJobStepSession", $logInRow["pu_key"], $month);
					}else{
						$hour = time() + 60 * 60 * 24;
						setcookie("ppJobStepSession", $logInRow["pu_key"], $hour);
					}
					
					$logDesc = "JobStep PP APP - Korisnik se prijavio. ";
					$logDate = date("Y-m-d H:i:s");

					$logQuery = $db->prepare("
						INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date, log_type)
						VALUES
						(:log_employeeid, :log_desc, :log_date, :log_type)
					");

					$logQuery->execute(array(
						':log_employeeid' => $logInRow["pu_id"],
						':log_desc' => $logDesc,
						':log_date' => $logDate,
						':log_type' => 10
					));
					
					echo 2;
					
				}else{
					echo 3;
				}
			}else{
				echo 1;
			}
		}else{
			echo 0;
		}
	break;
	
	case "check_email":
		// case koji provjerava da li mail postoji u bazi
		// 0 ako ne postoji
		// 1 ako postoji 
		
		$email 		= $_REQUEST['input_email'];

		$checkEmailQuery = $db->prepare("
			SELECT 
				pu_id
			FROM idk_pp_users
			WHERE pu_email = :pu_email AND pu_status = 1
		");
		$checkEmailQuery->execute(array(
			':pu_email' => $email
		));
		
		if($checkEmailQuery->rowCount() != 0){
			echo 1;
		}else{
			echo 0;
		}
	break;

	case "send_mail_password_recovery":
		// case koji šalje mail useru sa linkom gdje može upisati novi password
		$email 		= $_REQUEST['input_email'];
		$token		= getUserPuKeyFromEmail($email);
		$lang		= getLanguageForUserByToken($token);
		$link 		= getSiteUrlr()."/new_password?token=$token";
		$link_url	= '<a href="'.$link.'">'.$txtArray["Link za promjenu lozinke"][$lang].'</a>';

		$body 		= '<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
		</head>
		<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f0f0f0;">
			<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td align="center">
						<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 100%; margin: 0 auto;">
							<tr>
								<td style="padding: 16px 0; text-align: center;">
								 <img style=" height: 50px; width: 50px;  -webkit-user-select: none;margin: auto;cursor: zoom-in;background-color: none;transition: background-color 300ms;" src="https://s3-alpha-sig.figma.com/img/4e85/c218/a1c681a0fbafc7c5c8cb316be1435a59?Expires=1702857600&amp;Signature=ahnR4RhTYpCPJJD7axNDMnQy0n1HURQ9mD1NJTeNZ2ceT~fRxrKtN6Y1liYeukeUkUzFUtdK3YCFJ~soaw9jUjCmteUT1irVcVutWklCTdYxkn4NHoQ7wnPws9H53WwRxrfOShwwS7DscDnt2RXCx41EecuqY31h4k~PwuqLYD8SORV2bbBrxOkGdtK9NBN3l3jFULwQpgJJ94OM-MhBJIlK9pMpbnGMBUDh1Zwb-G-4P5bcNpA8CG5a-xN0IYecZwrgww9hsy3F0AaJfLVBnRHYZUWL0pmpcy3kOkdmLNSkf7BPhQ1Em1caPpPd7ZnhEVsWLT1IYkWebJfjSysSvg__&amp;Key-Pair-Id=APKAQ4GOSFWCVNEHN3O4" width="414" height="472"> 
								</td>
							</tr>
							<tr style="margin-left: 5%;">
								<td style="padding-top: 30px; padding-bottom: 64px; padding-left: 20px; padding-right: 20px; text-align: center; ">
									<p style="font-size: 30px; font-weight: 600; line-height:24px; color: #6097A0;">'.$link_url.'</p> 
									<p style="font-size: 16px; font-weight: 550; line-height: 24px; color: #344054;"><br>JobStep Team</p>
								  
								</td>
							</tr>
							<tr>
							  <td style="background-color: #6097A0; color: #fff; padding: 10px 20px; text-align: center;">
								<img src="'.getCRMUrlr().'jobstep-partner/mail-templates/jobstep-logo-white.png" style="height: 50px; width: 50px; -webkit-user-select: none; margin: auto; cursor: zoom-in; background-color: none; transition: background-color 300ms;">
								<p style="text-align: center; font-size: 12px; font-weight: 600; line-height: 24px;">JOBSTEP</p>
							  </td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</body>
		</html>';
		
		$subject    = $txtArray["Promjena lozinke"][$lang];

		$hostName = "smtp.gmail.com";
		$userName = "support@job-step.com";
		$password = "eooc nnxo aylp lqkh";
		$setFrom = "no-reply@job-step.com";
		$mail = new PHPMailer;
		$mail->isSMTP();											// Set mailer to use SMTP
		$mail->Host = $hostName;									// Specify main and backup SMTP servers
		$mail->SMTPAuth = true;										// Enable SMTP authentication
		$mail->Username = $userName;								// SMTP username
		$mail->Password = $password;								// SMTP password
		$mail->SMTPSecure = 'ssl';									// Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;											// TCP port to connect to
		$mail->CharSet = 'UTF-8';
		$mail->setFrom($setFrom, 'JobStep');    		// Add a recipient
		$mail->addAddress($email); 				// Add a recipient

		$mail->Subject = $subject;
		$mail->Body    = $body;
		$mail->AltBody = "Jobstep IT Solutions";

		if(!$mail->send()) {
			echo json_encode('false');
		}else{
			echo json_encode('true');
		}

	break;
	
	case "create_new_password":
		// case koji treba da kreira novi password
		$user_key 		= $_REQUEST['user_key'];
		$user_password	= $_REQUEST['input_password'];
		$user_hash_pw = md5($user_password);

		$checkUser = $db->prepare("
			SELECT 
				pu_id
			FROM idk_pp_users
			WHERE pu_key = :pu_key AND pu_status = 1
		");
		$checkUser->execute(array(
			':pu_key' => $user_key
		));
		
		if($checkUser->rowCount() != 0){
			$updatePassword = $db -> prepare("
			UPDATE idk_pp_users
				SET 
					pu_password = :pu_password
				WHERE 
					pu_key = :pu_key
			");
			
			$updatePassword -> execute(array(
				':pu_key' 		=> $user_key,
				':pu_password' 	=> $user_hash_pw
			));

			$log_desc = "Kandidat [".getUserPPIdFromKey($user_key)."] promjenio sifru: ".$user_password;
			addToLogs($log_desc);
		}else{
			echo 0;
		}

	break;
	
	case "get_view_choice":
		// case koji vraća view choice (superadmin i admin link) u zavisnosti od toga ako je user i superadmin i admin
		// 1 - superadmin 2 - admin 0 - nista nema dodano
		// $provjera = getUserTypeR($userId);
		// var_dump($provjera);
		// if($provjera == 1 AND $userId != 16 AND $userId != 17 AND $userId != 19){
		
			?>
			<!--<div class = "row">
				<div class = "col-6">
					<div style = "width:100%;text-align:center;">
						<a href = "#" class = "view_choice view_choice_superadmin view_choice_superadmin_selected div_highlight" id = "view_choice_superadmin">Superadmin</a>
					</div>
				</div>
				<div class = "col-6">
					<div style = "width:100%;text-align:center;">
						<a href = "#" class = "view_choice view_choice_admin div_highlight" id = "view_choice_admin">Admin</a>
					</div>
				</div>
			</div>-->
			<?php
		// }
	break;
	
	case "get_progress_bar":
		//sleep(1);
		/*
		case koji vraća circular progress barove u odnosu na nalog_id, type (govori da li je za projekte ili statuse prijave) i 
		bar_id koji govori koji je tačno progress bar u pitanju
		*/
		$nalog_ids	 	= $_REQUEST['nalog_ids'];
		$partner_ids 	= $_REQUEST['partner_ids'];
		$type 			= $_REQUEST['type'];
		// var_dump($nalog_ids);
		// var_dump($partner_ids);
		// var_dump($type);

		$flag_count_from_project 			= false;
		$flag_count_from_status_prijave 	= false;
		$flag_count_from_dipl_and_language 	= false;
		$flag_count_from_visa_process	 	= false;
		$userAccess = getUserAccess($userId);
		// var_dump($userAccess['superAdminAccess']['nalog'][0]);
		// exit();	
		if($type == 0){
			$user_type = getUserTypeR($userId);
			$userAccess = getUserAccess($userId);
			
			if($user_type == 1){
				$nalog_ids = $userAccess['superAdminAccess']['nalog'][0];
				$flag_count_from_project = true;
			}else{
				// $nalog_ids 		= $userAccess['superAdminAccess']['nalog'][0];
				$nalog_ids 		= $userAccess['adminAccess']['nalog'][0];
				$partner_ids	= $userAccess['adminAccess']['partner'][0];
				$flag_count_from_status_prijave = true;
			}
		}
		else if($nalog_ids == 0 AND $partner_ids == 0 AND $type == 1){
			$user_type = getUserTypeR($userId);
			$userAccess = getUserAccess($userId);
			$nalog_ids = $userAccess['superAdminAccess']['nalog'][0];
			$flag_count_from_project = true;
		}
		else if($nalog_ids == 0 AND $partner_ids == 0 AND $type == 2){
				$nalog_ids 		= $userAccess['superAdminAccess']['nalog'][0];
				$partner_ids	= $userAccess['adminAccess']['partner'][0];
				$flag_count_from_status_prijave = true;		
		}
		else if($nalog_ids != 0 AND $partner_ids != 0 AND $type == 3){
			$flag_count_from_dipl_and_language = true;
		}
		else if($nalog_ids != 0 AND $partner_ids != 0 AND $type == 4){
			$flag_count_from_visa_process = true;
		}
		else if($nalog_ids != 0 AND $partner_ids == 0 AND $type == 1){
			$flag_count_from_project = true;
		}
		else if($nalog_ids != 0 AND $partner_ids == 0 AND $type == 2){
			$flag_count_from_status_prijave = true;
			$nalog_ids 		= $userAccess['superAdminAccess']['nalog'][0];
			$partner_ids	= $userAccess['adminAccess']['partner'][0];
		}
		else if($nalog_ids != 0 AND $partner_ids != 0){
			$flag_count_from_status_prijave = true;
		}
		else if ($nalog_ids == 0 AND $partner_ids != 0){
			$flag_count_from_status_prijave = true;
		}
		// $nalog_ids = 222;
		if(is_array($nalog_ids)){
			if (($key = array_search('222', $nalog_ids)) !== false) {
				unset($nalog_ids[$key]);
				array_push($nalog_ids, '217,218,219,220,222');
			}
		}
		else if($nalog_ids == '222'){
			$nalog_ids = '217,218,219,220,222';
		}
		if(is_array($nalog_ids)){
			$nalog_ids = implode(',', $nalog_ids);
		}
		if(is_array($partner_ids)){
			$partner_ids = implode(',', $partner_ids);

			
		}
		// var_dump($nalog_ids);
		// var_dump($partner_ids);
		// var_dump($flag_count_from_project);
		// var_dump($flag_count_from_status_prijave);
		// var_dump($flag_count_from_dipl_and_language);
		// var_dump($flag_count_from_visa_process);

		if($flag_count_from_project){

			$query_get_applied_candidates = $db -> prepare("
				SELECT COUNT(pk.pk_kandidatid) as applied_candidates
				FROM idk_project_kandidati pk 
				JOIN idk_projects pr 
				ON pr.project_id = pk.pk_projectid 
				WHERE pr.project_nalogid IN (".$nalog_ids.")
			");
			$query_get_applied_candidates -> execute();
			$row_get_applied_candidates = $query_get_applied_candidates-> fetch();
						
			$applied_candidates = $row_get_applied_candidates['applied_candidates'];
			
			$query_get_candidates_for_pie = $db -> prepare("
				SELECT 
					SUM(CASE WHEN table_grouped_by_candidate_id.project_type LIKE ('Casting') THEN 1 ELSE 0 END) as sum_casting,
					SUM(CASE WHEN table_grouped_by_candidate_id.project_type LIKE ('Intervju') THEN 1 ELSE 0 END) as sum_interview,
					SUM(CASE WHEN table_grouped_by_candidate_id.project_type LIKE ('Odbijen') THEN 1 ELSE 0 END) as sum_rejected,
					SUM(CASE WHEN table_grouped_by_candidate_id.project_type LIKE ('Ugovor') THEN 1 ELSE 0 END) as sum_passed
				FROM (
					SELECT
						pr.project_type, kan.kandidat_id
					FROM(
						SELECT 
							sqpr.project_id,
							CASE 
								WHEN sqpr.project_name LIKE ('%- Casting%')
								THEN 'Casting'
								WHEN sqpr.project_name LIKE ('%- Intervju%')
								THEN 'Intervju'
								WHEN sqpr.project_name LIKE ('%- Odbijen%')
								THEN 'Odbijen'
								WHEN (sqpr.project_name LIKE ('%- Ugovor%') OR sqpr.project_name LIKE ('%Kandidati poceli sa radom%') OR sqpr.project_name LIKE ('%Zavrseni kandidati'))
								THEN 'Ugovor'
							END as project_type
						FROM 
							idk_projects sqpr
						WHERE 
							sqpr.project_nalogid IN (".$nalog_ids.")
						AND (
							sqpr.project_name LIKE ('%- Casting%')
							OR	sqpr.project_name LIKE ('%- Intervju%')
							OR  sqpr.project_name LIKE ('%- Odbijen%')
							OR  sqpr.project_name LIKE ('%- Ugovor%')
							OR 	sqpr.project_name LIKE ('%Kandidati poceli sa radom%')
							OR 	sqpr.project_name LIKE ('%Zavrseni kandidati')
						)
					
					) pr
					JOIN idk_project_kandidati pk
					ON pk.pk_projectid = pr.project_id
					JOIN idk_kandidati kan
					on kan.kandidat_id = pk.pk_kandidatid
					WHERE kan.kandidat_status != 3
					GROUP BY kan.kandidat_id
				) table_grouped_by_candidate_id;
			");
			// var_dump($query_get_candidates_for_pie);
			$query_get_candidates_for_pie -> execute();
			
			$row_get_candidates_for_pie = $query_get_candidates_for_pie->fetch();
			
			
			$sum_casting 	= $row_get_candidates_for_pie['sum_casting'];
			$sum_interview 	= $row_get_candidates_for_pie['sum_interview'];
			$sum_passed 	= $row_get_candidates_for_pie['sum_passed'];
			$sum_rejected 	= $row_get_candidates_for_pie['sum_rejected'];
			
			
			if(is_null($sum_casting)){
				$sum_casting 	= 0;
			}
			if(is_null($sum_interview)){
				$sum_interview 	= 0;
			}
			if(is_null($sum_passed)){
				$sum_passed		= 0;
			}
			if(is_null($sum_rejected)){
				$sum_rejected 	= 0;
			}
			
			$circle_bar_max 	= $sum_casting + $sum_interview + $sum_passed + $sum_rejected;
			
			
			
			$code_to_append = '
				<div class = "row col-2 offset-5" type = "1" bar_id = "1">
					<p style = "text-align:center; font-weight: 500; font-size: 42px; line-height: 50px; margin-bottom:0px!important;" id = "applied_candidates" class = "text_color"></p>
					<p style = "text-align:center; font-weight: 500; font-size: 20px; line-height: 24px;" class = "text_color_secondary">'.$txtArray['Prijavljenih'][$languageUser].'</p>
				</div>
				<div class = "row col-10 offset-1">
					<div class = "col-md-3" style = "text-align:center;"  type = "1" bar_id = "2">
						<div id = "pb_casting" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
					<div class = "col-md-3 progress_bar_click" style = "text-align:center;"  type = "1" bar_id = "3">
						<div id = "pb_interview" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
					<div class = "col-md-3" style = "text-align:center;"  type = "1" bar_id = "5">
						<div id = "pb_rejected" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
					<div class = "col-md-3 progress_bar_click" style = "text-align:center;"  type = "1" bar_id = "4">
						<div id = "pb_passed" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
				</div>
			';
			$progress_bars = array(
				"returns_status_bar" 	=> false,
				"applied_candidates" 	=> $applied_candidates,
				"code_to_append" 		=> $code_to_append,
				"bar_type" 				=> array("#pb_casting","#pb_interview","#pb_passed","#pb_rejected"),
				"bar_style" 			=> array("color_casting","color_interview","color_passed","color_rejected"),
				"bar_value" 			=> array($sum_casting, $sum_interview, $sum_passed, $sum_rejected),
				"bar_max" 				=> array($circle_bar_max, $circle_bar_max, $circle_bar_max, $circle_bar_max),
				"bar_text" 				=> array(
											$txtArray['Casting'][$languageUser],
											$txtArray['Intervju'][$languageUser],
											$txtArray['Prihvaćen'][$languageUser],
											$txtArray['Odbijen'][$languageUser]
										)
			);
			echo json_encode($progress_bars);
		}
		else if($flag_count_from_status_prijave == 2){
			//var_dump($partner_ids);
			$query_get_candidates_for_pie = $db->prepare("
				SELECT 
					SUM(
						CASE
							WHEN kandidat_status_prijave = 7
							THEN 1
							ELSE 0
						END
					) as sum_waiting_contract,
					SUM(
						CASE
							WHEN kandidat_status_prijave = 8
							THEN 1
							ELSE 0
						END
					) as sum_contract_sent,
					SUM(
						CASE
							WHEN kandidat_status_prijave = 9
							THEN 1
							ELSE 0
						END
					) as sum_contract_signed,
					SUM(
						CASE
							WHEN kandidat_status_prijave = 4
							THEN 1
							ELSE 0
						END
					) as sum_started_working
				FROM idk_kandidati
				WHERE kandidat_nalog_id IN (".$nalog_ids.")
				AND kandidat_ppa_partner_id IN (".$partner_ids.")
			");
			// var_dump($query_get_candidates_for_pie);

			$query_get_candidates_for_pie -> execute();
			$row_get_candidates_for_pie = $query_get_candidates_for_pie -> fetch();
			
			$sum_waiting_contract 	= $row_get_candidates_for_pie['sum_waiting_contract'];
			$sum_contract_sent 		= $row_get_candidates_for_pie['sum_contract_sent'];
			$sum_contract_signed 	= $row_get_candidates_for_pie['sum_contract_signed'];
			$sum_started_working 	= $row_get_candidates_for_pie['sum_started_working'];
			
			if(is_null($sum_waiting_contract))	{$sum_waiting_contract 	= 0;}
			if(is_null($sum_contract_sent))		{$sum_contract_sent 	= 0;}
			if(is_null($sum_contract_signed))	{$sum_contract_signed 	= 0;}
			if(is_null($sum_started_working))	{$sum_started_working 	= 0;}
			
			$circle_bar_max 		= $sum_waiting_contract + $sum_contract_sent + $sum_contract_signed; 
			$code_to_append = '
				<div class = "row col-10 offset-1 mt-5">
					<div class = "col-md-4 progress_bar_click" style = "text-align:center;" type = "2" bar_id = "2">
						<div id = "pb_waiting_contract" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
					<div class = "col-md-4 progress_bar_click" style = "text-align:center;" type = "2" bar_id = "3">
						<div id = "pb_contract_sent" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2" ></div>
					</div>
					<div class = "col-md-4 progress_bar_click" style = "text-align:center;"  type = "2" bar_id = "5">
						<div id = "pb_contract_signed" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
				</div>
			';
			$progress_bars = array(
				"returns_status_bar" 	=> true,
				"applied_candidates" 	=> NULL,
				"code_to_append" 		=> $code_to_append,
				"bar_type" 				=> array("#pb_waiting_contract","#pb_contract_sent","#pb_contract_signed"),
				"bar_style" 			=> array("color_waiting_contract","color_contract_sent","color_contract_signed"),
				"bar_max" 				=> array($circle_bar_max, $circle_bar_max, $circle_bar_max),
				"bar_value" 			=> array($sum_waiting_contract, $sum_contract_sent, $sum_contract_signed),
				"bar_text" 				=> array(
					'<tspan x="50" dy="1.2em">'.$txtArray['ceka_ugovor_1'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$txtArray['ceka_ugovor_2'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$sum_waiting_contract.'</tspan>',
					'<tspan x="50" dy="1.2em">'.$txtArray['poslan_ugovor_1'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$txtArray['poslan_ugovor_2'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$sum_contract_sent.'</tspan>',
					'<tspan x="50" dy="1.2em">'.$txtArray['potpisan_ugovor_1'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$txtArray['potpisan_ugovor_2'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$sum_contract_signed.'</tspan>',
				)
			);
			echo json_encode($progress_bars);
		}
		else if($flag_count_from_dipl_and_language){
			$sum_nostrification_pending 	= 0;
			$sum_nostrification_started 	= 0;
			$sum_nostrification_finished 	= 0;
			$max_nostrification				= 0;

			$query_get_nostrification_status_count = $db -> prepare('
				SELECT
					SUM(CASE WHEN ((nd.status_nd_kandidata IN (1,2,7) OR nd.status_nd_kandidata IS NULL) AND (kan.kandidat_ima_nostrifikaciju IS NULL OR kan.kandidat_ima_nostrifikaciju = 0)) THEN 1 ELSE 0 END) as cnt_pending,
					SUM(CASE WHEN nd.status_nd_kandidata IN (3,4,5)  AND (kan.kandidat_ima_nostrifikaciju IS NULL OR kan.kandidat_ima_nostrifikaciju = 0) THEN 1 ELSE 0 END) as cnt_started,
					SUM(CASE WHEN nd.status_nd_kandidata = 6  OR kan.kandidat_ima_nostrifikaciju = 1 THEN 1 ELSE 0 END) as cnt_finished
				
				FROM
					(
					SELECT
						kandidat_dipl_id, kandidat_ima_nostrifikaciju
					FROM
						idk_kandidati
					WHERE
						kandidat_status_prijave = 9 AND kandidat_ppa_partner_id IN('.$partner_ids.')
				) kan
				LEFT JOIN idk_nd_kandidata nd
				ON kan.kandidat_dipl_id = nd.id_broj_nd_kandidata
			');

			$query_get_nostrification_status_count -> execute();
			if($query_get_nostrification_status_count->rowCount()){
				$row_get_nostrification_status_count = $query_get_nostrification_status_count -> fetch();

				$sum_nostrification_pending 	= intval($row_get_nostrification_status_count['cnt_pending']);
				$sum_nostrification_started 	= intval($row_get_nostrification_status_count['cnt_started']);
				$sum_nostrification_finished 	= intval($row_get_nostrification_status_count['cnt_finished']);
				$max_nostrification				= $sum_nostrification_pending + $sum_nostrification_started + $sum_nostrification_finished; 	
			}

			$candidates_all = array();
			$candidates_exist_in_cvl = array();
			$candidates_doesnt_exist_in_kj = array();
			$candidates_to_find_max_language = array();

			$query_get_needed_candidates = $db -> prepare("
				SELECT 
					kan.kandidat_id, 
					cvl.exists_in_cvl, 
					kj.exists_in_kj
				FROM(
					SELECT sqkan.kandidat_id
					FROM idk_kandidati sqkan
					WHERE sqkan.kandidat_ppa_partner_id IN(".$partner_ids.")
					AND sqkan.kandidat_status_prijave = 9                
				)kan 
				
				LEFT JOIN (
					SELECT sqkj.kj_kandidatid, 1 as exists_in_cvl
					FROM idk_kandidat_jezici sqkj
					WHERE sqkj.kj_id IN (
						SELECT cvl.cvl_id
						FROM idk_candidate_verified_languages cvl
						WHERE cvl.cvl_active = 1
					)
				)cvl
				ON 
					cvl.kj_kandidatid = kan.kandidat_id

				LEFT JOIN (
					SELECT ssqkj.kj_kandidatid, 1 as exists_in_kj
						FROM idk_kandidat_jezici ssqkj
					WHERE ssqkj.kj_naziv LIKE ('Njemački')
				)kj
				ON 
					kj.kj_kandidatid = kan.kandidat_id
				AND 
					cvl.exists_in_cvl IS NULL  
			");

        $query_get_needed_candidates -> execute();

        while($row_get_needed_candidates = $query_get_needed_candidates -> fetch()){
            $cte_candidate_id   = $row_get_needed_candidates['kandidat_id'];
            $cte_exists_in_cvl  = $row_get_needed_candidates['exists_in_cvl'];
            $cte_existnt_in_kj  = $row_get_needed_candidates['exists_in_kj'];
            array_push($candidates_all, $cte_candidate_id);
            if($cte_exists_in_cvl == 1){
                array_push($candidates_exist_in_cvl, $cte_candidate_id);
            }
            else if(is_null($cte_existnt_in_kj)){
                array_push($candidates_doesnt_exist_in_kj, $cte_candidate_id);
            }
        
        }

        $candidates_to_find_max_language = array_diff($candidates_all, $candidates_exist_in_cvl, $candidates_doesnt_exist_in_kj);

        $condition_all                  = "";
        $condition_exists_in_cvl        = "";
        $condition_existnt_in_kj        = "";
        $condition_find_max_language    = "";

        if(!count($candidates_all)){
			array_push($candidates_all, '-1');
        }
        if(!count($candidates_exist_in_cvl)){
			array_push($candidates_exist_in_cvl, '-1');
        }
        if(!count($candidates_doesnt_exist_in_kj)){
			array_push($candidates_doesnt_exist_in_kj, '-1');
        }
        if(!count($candidates_to_find_max_language)){
			array_push($candidates_to_find_max_language, '-1');
        }
		
			$query_get_language_self_assessment = $db -> prepare("
				SELECT
					SUM(
						CASE 
							WHEN table_to_count.language_level IN('0', '') OR table_to_count.language_level IS NULL 
							THEN 1 
							ELSE 0
						END
					) AS sum_a0,
					SUM(
						CASE 
							WHEN table_to_count.language_level LIKE('1') 
							THEN 1 
							ELSE 0
						END
					) AS sum_a1,
					SUM(
						CASE 
							WHEN table_to_count.language_level LIKE('2') 
							THEN 1 
							ELSE 0
						END
					) AS sum_a2,
					SUM(
						CASE 
							WHEN table_to_count.language_level IN('3', '4', '5', '6') 
							THEN 1 
							ELSE 0
						END
					) AS sum_b1
				FROM(
					SELECT
                        set1_kj.kj_kandidatid,
                        set1_kj.language_level,
                        set1_cvl.cvl_status,
                        set1_cvl.cvl_exam_date,
                        set1_cvl.jezik_dana_na_statusu

                    FROM(
                        SELECT
                            set1_sqcvl.cvl_id,
                            set1_sqcvl.cvl_status,
                            set1_sqcvl.cvl_exam_date,
                            DATEDIFF(CURRENT_TIME(), set1_sqcvl.cvl_last_updated) as jezik_dana_na_statusu
                        FROM
                            idk_candidate_verified_languages set1_sqcvl
                        WHERE
                            set1_sqcvl.cvl_active = 1
                    ) set1_cvl
                    JOIN(
                        SELECT set1_sqkj.kj_id,
                            set1_sqkj.kj_kandidatid,
                            CASE 
                                WHEN set1_sqkj.kj_slusanje = 'C2'
                                THEN 6
                                WHEN set1_sqkj.kj_slusanje = 'C1'
                                THEN 5
                                WHEN set1_sqkj.kj_slusanje = 'B2'
                                THEN 4
                                WHEN set1_sqkj.kj_slusanje = 'B1'
                                THEN 3
                                WHEN set1_sqkj.kj_slusanje = 'A2'
                                THEN 2
                                WHEN set1_sqkj.kj_slusanje = 'A1'
                                THEN 1
                                ELSE 0
                            END as language_level
                        FROM
                            idk_kandidat_jezici set1_sqkj
                        WHERE
                            set1_sqkj.kj_naziv LIKE('Njemački')
                        AND set1_sqkj.kj_kandidatid IN (".implode(',', $candidates_exist_in_cvl).")
                    ) set1_kj
                    ON
                        set1_kj.kj_id = set1_cvl.cvl_id

                    UNION

                    SELECT 
                        set2_kj.kj_kandidatid, 
                        max(set2_kj.language_level) as language_level, 
                        NULL as cvl_status, 
                        NULL as cvl_exam_date, 
                        NULL as jezik_dana_na_statusu
                    FROM(
                        SELECT 
                            set2_sqkj.kj_id,
                            set2_sqkj.kj_kandidatid,
                            CASE 
                                WHEN set2_sqkj.kj_slusanje = 'C2'
                                THEN 6
                                WHEN set2_sqkj.kj_slusanje = 'C1'
                                THEN 5
                                WHEN set2_sqkj.kj_slusanje = 'B2'
                                THEN 4
                                WHEN set2_sqkj.kj_slusanje = 'B1'
                                THEN 3
                                WHEN set2_sqkj.kj_slusanje = 'A2'
                                THEN 2
                                WHEN set2_sqkj.kj_slusanje = 'A1'
                                THEN 1
                                ELSE 0
                            END as language_level
                        FROM 
                            idk_kandidat_jezici set2_sqkj
                        WHERE 
                            set2_sqkj.kj_naziv LIKE ('Njemački')
                        AND set2_sqkj.kj_kandidatid IN (".implode(',', $candidates_to_find_max_language).")
                    )set2_kj
                    GROUP BY 
                        set2_kj.kj_kandidatid

                    UNION

                    SELECT 
                        set3_kan.kandidat_id, 
                        0 as language_level, 
                        NULL as cvl_status, 
                        NULL as cvl_exam_date, 
                        NULL as jezik_dana_na_statusu
                    FROM 
                        idk_kandidati set3_kan
                    WHERE 
                        set3_kan.kandidat_id IN(".implode(',', $candidates_doesnt_exist_in_kj).")
				) table_to_count
			");
			// var_dump($query_get_language_self_assessment);
			$query_get_language_self_assessment -> execute();
			$row_get_language_self_assessment = $query_get_language_self_assessment -> fetch();
			
			$sum_language_A0 	= intval($row_get_language_self_assessment['sum_a0']);
			$sum_language_A1 	= intval($row_get_language_self_assessment['sum_a1']);
			$sum_language_A2 	= intval($row_get_language_self_assessment['sum_a2']);
			$sum_language_B1 	= intval($row_get_language_self_assessment['sum_b1']);
			$max_language		= $sum_language_A0 + $sum_language_A1 + $sum_language_A2 + $sum_language_B1; 


			$code_to_append = '
				<div class = "row col-2 offset-5 mt-5" type = "1" bar_id = "1">
					<p style = "text-align:center; font-weight: 500; font-size: 20px; line-height: 24px;" class = "text_color_secondary">'.$txtArray['Nostrifikacija'][$languageUser].'</p>
				</div>
				<div class = "row col-10 offset-1 mt-2" style = "text-align:center;">
					<div class = "col-md-4 progress_bar_click" style = "text-align:center;" type = "3" bar_id = "1">
						<div id = "pb_nostrification_pending" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
					<div class = "col-md-4 progress_bar_click" style = "text-align:center;" type = "3" bar_id = "2">
						<div id = "pb_nostrification_started" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2" ></div>
					</div>
					<div class = "col-md-4 progress_bar_click" style = "text-align:center;"  type = "3" bar_id = "3">
						<div id = "pb_nostrification_finished" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
				</div>
				<div class = "row">
					<hr class = "col-4" style = "margin:auto;">
					<div class = "col-md-3 progress_bar_click" style = "text-align:center;" type = "3" bar_id = "8">
						<div id = "pb_language_nostrification" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
					<hr class = "col-4" style = "margin:auto;">
				</div>
				<div class = "row col-2 offset-5" type = "1" bar_id = "1">
					<p style = "text-align:center; font-weight: 500; font-size: 20px; line-height: 24px;" class = "text_color_secondary">'.$txtArray['Certifikat jezika'][$languageUser].'</p>
				</div>
				<div class = "row col-10 offset-1 mt-2">
					<div class = "col-md-3 progress_bar_click" style = "text-align:center;" type = "3" bar_id = "4">
						<div id = "pb_language_A0" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
					<div class = "col-md-3 progress_bar_click" style = "text-align:center;" type = "3" bar_id = "5">
						<div id = "pb_language_A1" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2" ></div>
					</div>
					<div class = "col-md-3 progress_bar_click" style = "text-align:center;"  type = "3" bar_id = "6">
						<div id = "pb_language_A2" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
					<div class = "col-md-3 progress_bar_click" style = "text-align:center;"  type = "3" bar_id = "7">
						<div id = "pb_language_B1" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
				</div>
			';

			$progress_bars = array(
				"returns_status_bar" 	=> true,
				"applied_candidates" 	=> NULL,
				"code_to_append" 		=> $code_to_append,
				"bar_type" 				=> array("#pb_language_nostrification","#pb_nostrification_pending","#pb_nostrification_started","#pb_nostrification_finished","#pb_language_A0","#pb_language_A1","#pb_language_A2","#pb_language_B1"),
				"bar_style" 			=> array("color_interview","color_nostrification_pending","color_nostrification_started","color_nostrification_finished","color_language_A0","color_language_A1","color_language_A2","color_language_B1"),
				"bar_max" 				=> array($max_nostrification, $max_nostrification, $max_nostrification, $max_nostrification, $max_language, $max_language, $max_language, $max_language),
				"bar_value" 			=> array($max_nostrification, $sum_nostrification_pending, $sum_nostrification_started, $sum_nostrification_finished, $sum_language_A0, $sum_language_A1, $sum_language_A2, $sum_language_B1),
				"bar_text" 				=> array(
					'<tspan x="50" dy="1.2em"></tspan><tspan x="50" dy="1.2em">'.$txtArray['Svi'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$max_nostrification.'</tspan>',
					'<tspan x="50" dy="1.2em"></tspan><tspan x="50" dy="1.2em">'.$txtArray['U pripremi'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$sum_nostrification_pending.'</tspan>',
					'<tspan x="50" dy="1.2em"></tspan><tspan x="50" dy="1.2em">'.$txtArray['Zatražen'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$sum_nostrification_started.'</tspan>',
					'<tspan x="50" dy="1.2em"></tspan><tspan x="50" dy="1.2em">'.$txtArray['Završeno'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$sum_nostrification_finished.'</tspan>',
					'<tspan x="50" dy="1.2em"></tspan><tspan x="50" dy="1.2em">'.$txtArray['Bez znanja'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$sum_language_A0.'</tspan>',
					'<tspan x="50" dy="1.2em"></tspan><tspan x="50" dy="1rem" style="font-size: 22px;fill: #999999;">A1</tspan><tspan x="50" dy="1rem">'.$sum_language_A1.'</tspan>',
					'<tspan x="50" dy="1.2em"></tspan><tspan x="50" dy="1rem" style="font-size: 22px;fill: #999999;">A2</tspan><tspan x="50" dy="1rem">'.$sum_language_A2.'</tspan>',
					'<tspan x="50" dy="1.2em"></tspan><tspan x="50" dy="1rem" style="font-size: 22px;fill: #999999;">B1+</tspan><tspan x="50" dy="1rem">'.$sum_language_B1.'</tspan>'
				)
			);
			echo json_encode($progress_bars);
		}
		else if($flag_count_from_visa_process){
			$query_get_visa_process_candidate_count = $db -> prepare("
				SELECT 
					SUM(
						CASE
							WHEN 
								kandidat_status_prijave IN (12,21)
								OR (
									kandidat_status_prijave = 24
									AND	kandidat_id IN (
										SELECT sqvi.vi_cand_id
										FROM idk_pp_visa_incomplete sqvi
										WHERE 
											sqvi.vi_status = 1
										AND	sqvi.vi_nalog_id IN (".$nalog_ids.")
										AND (
												sqvi.vi_type = 1
											OR(
													sqvi.vi_type = 2
												AND sqvi.vi_complaint = 1
											)
										)
									)
								)
							THEN 1
							ELSE 0
						END
					) as sum_collecting_documents,
					SUM(
						CASE
							WHEN kandidat_status_prijave IN (15, 18)
							THEN 1
							ELSE 0
						END
					) as sum_waiting_visa,
					SUM(
						CASE
							WHEN 
								kandidat_status_prijave = 27
								OR (
									kandidat_status_prijave = 24
									AND	kandidat_id IN (
										SELECT sqvi.vi_cand_id
										FROM idk_pp_visa_incomplete sqvi
										WHERE 
											sqvi.vi_status = 1
										AND	sqvi.vi_nalog_id IN (".$nalog_ids.")
										AND (
												sqvi.vi_type = 1
											OR(
													sqvi.vi_type = 2
												AND sqvi.vi_complaint = 0
											)
										)
									)
								)
							THEN 1
							ELSE 0
						END
					) as sum_result_of_visa,
					SUM(
						CASE
							WHEN kandidat_status_prijave IN (4,10)
							THEN 1
							ELSE 0
						END
					) as sum_started_working
				FROM idk_kandidati
				WHERE kandidat_nalog_id IN (".$nalog_ids.")
				AND kandidat_ppa_partner_id IN (".$partner_ids.")

			");
			// var_dump($query_get_visa_process_candidate_count);
			// exit();
			$query_get_visa_process_candidate_count -> execute();
			$row_get_visa_process_candidate_count = $query_get_visa_process_candidate_count -> fetch();

			$sum_collecting_documents 	= intval($row_get_visa_process_candidate_count['sum_collecting_documents']);
			$sum_waiting_visa 			= intval($row_get_visa_process_candidate_count['sum_waiting_visa']);
			$sum_result_of_visa 		= intval($row_get_visa_process_candidate_count['sum_result_of_visa']);
			$sum_started_working 		= intval($row_get_visa_process_candidate_count['sum_started_working']);

			$max_visa_status			= $sum_collecting_documents + $sum_waiting_visa + $sum_result_of_visa + $sum_started_working;

			$code_to_append = '
				<div class = "row col-10 offset-1 mt-5">
					<div class = "col-md-3 progress_bar_click" style = "text-align:center;" type = "4" bar_id = "1">
						<div id = "pb_colecting_documents" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
					<div class = "col-md-3 progress_bar_click" style = "text-align:center;" type = "4" bar_id = "2">
						<div id = "pb_waiting_visa" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2" ></div>
					</div>
					<div class = "col-md-3 progress_bar_click" style = "text-align:center;"  type = "4" bar_id = "3">
						<div id = "pb_result_of_visa" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
					<div class = "col-md-3 progress_bar_click" style = "text-align:center;"  type = "4" bar_id = "4">
						<div id = "pb_started_working" class="col-3 progress circle-progress circle-progress-value circle-progress-circle circle-progress-text p-2"></div>
					</div>
				</div>
			';

			$progress_bars = array(
				"returns_status_bar" 	=> true,
				"applied_candidates" 	=> NULL,
				"code_to_append" 		=> $code_to_append,
				"bar_type" 				=> array("#pb_colecting_documents","#pb_waiting_visa","#pb_result_of_visa","#pb_started_working"),
				"bar_style" 			=> array("color_colecting_documents","color_waiting_visa","color_result_of_visa","color_started_working"),
				"bar_max" 				=> array($max_visa_status, $max_visa_status, $max_visa_status, $max_visa_status),
				"bar_value" 			=> array($sum_collecting_documents, $sum_waiting_visa, $sum_result_of_visa, $sum_started_working),
				"bar_text" 				=> array(
					'<tspan x="50" dy="0.7em">'.$txtArray['Skuplja dokumentaciju 1'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$txtArray['Skuplja dokumentaciju 2'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$sum_collecting_documents.'</tspan>',
					'<tspan x="50" dy="1.2em"></tspan><tspan x="50" dy="1.2em">'.$txtArray['Čeka vizu'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$sum_waiting_visa.'</tspan>',
					'<tspan x="50" dy="1.2em"></tspan><tspan x="50" dy="1.2em">'.$txtArray['Rezultat'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$sum_result_of_visa.'</tspan>',
					'<tspan x="50" dy="1.2em"></tspan><tspan x="50" dy="1.2em">'.$txtArray['Počeo raditi'][$languageUser].'</tspan><tspan x="50" dy="1.2em">'.$sum_started_working.'</tspan>'
				)
			);
			echo json_encode($progress_bars);
		}
	break;
	
	case "get_list_nalozi":
		$type 		= $_REQUEST['type'];
		$provjera = getUserTypeR($userId);
		$userAccess = getUserAccess($userId);
		$result = array();
		if($type == 1){
			//SuperAdmin
			$userTypeTxt = "superAdminAccess";
		}else if($type == 2){
			//Admin
			$userTypeTxt = "adminAccess";
		}else{
			//Default
			if($provjera == 1){
				//SuperAdmin
				$userTypeTxt = "superAdminAccess";
			}else{
				//Admin
				$userTypeTxt = "adminAccess";
			}
		}
		//var_dump("Type:" .$type." Provjera:" .$provjera." USer:".$userTypeTxt);
		//var_dump($userAccess);
		$count = 0;
		// if($userTypeTxt == "superAdminAccess"){
			foreach($userAccess[$userTypeTxt]["count"] AS $valueCount){
				$nalogId = $userAccess[$userTypeTxt]["nalog"][$valueCount];
				$rowResult = '';
				if($valueCount != 0){
					$rowResult = '
						<tr class = "row_handle_nalog_click" nalog_id = "'.$nalogId.'" company_enpal = "'.getCompanyAccessR($nalogId).'">
							<td class = "table_nalog_icon_column">
								<i class="fa fa-files-o table_nalog_icon" aria-hidden="true"></i>
							</td>
							<td class = "table_text">'.getNazivNalogaR($nalogId).' - '.getCompanyNameR(getCompanyIdFromNalogR($nalogId)).'</td>
						</tr>
					';
					
					array_push($result, $rowResult);
				}else{
					$rowResult = '
						<tr class = "row_handle_nalog_click" nalog_id = "'.$nalogId.'" company_enpal = "'.getCompanyAccessR($nalogId).'">
							<td class = "table_nalog_icon_column table_nalog_icon_selected">
								<i class="fa fa-files-o table_nalog_icon" aria-hidden="true"></i>
							</td>
							<td class = "table_text table_nalog_selected">'.getNazivNalogaR($nalogId).' - '.getCompanyNameR(getCompanyIdFromNalogR($nalogId)).'</td>
						</tr>
					';
					array_push($result, $rowResult);
				}
			}
		// }
		// else{
		// 	foreach($userAccess[$userTypeTxt]["count"] AS $valueCount){
		// 		$nalogId = $userAccess[$userTypeTxt]["nalog"][$valueCount];
		// 		$partnerId = $userAccess[$userTypeTxt]["partner"][$valueCount];
		// 		$rowResult = '';
		// 		if($valueCount != 0){
		// 			$rowResult = '
		// 				<tr class = "row_handle_nalog_click" nalog_id = "'.$nalogId.'" partner_id = "'.$partnerId.'">
		// 					<td class = "table_nalog_icon_column">
		// 						<i class="fa fa-files-o table_nalog_icon" aria-hidden="true"></i>
		// 					</td>
		// 					<td class = "table_text">'.getNazivNalogaR($nalogId).' - '.getCompanyNameR(getCompanyForPartnerIdR($partnerId)).'</td>
		// 				</tr>
		// 			';
					
		// 			array_push($result, $rowResult);
		// 		}else{
		// 			$rowResult = '
		// 				<tr class = "row_handle_nalog_click" nalog_id = "'.$nalogId.'" partner_id = "'.$partnerId.'">
		// 					<td class = "table_nalog_icon_column table_nalog_icon_selected">
		// 						<i class="fa fa-files-o table_nalog_icon" aria-hidden="true"></i>
		// 					</td>
		// 					<td class = "table_text table_nalog_selected">'.getNazivNalogaR($nalogId).' - '.getCompanyNameR(getCompanyForPartnerIdR($partnerId)).'</td>
		// 				</tr>
		// 			';
		// 			array_push($result, $rowResult);
		// 		}
		// 	}
		// }
		
	?>
		<p class = "text_color" style = "font-weight: 600; font-size: 18px; line-height: 22px; padding-bottom:10px;"><?php echo $txtArray['Nalog'][$languageUser]; ?>:</p>
		<div id = "box_nalozi" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); overflow: auto; height: 15rem;" >
			<table class = "table_text" style = "width:95%">
				<?php 
					foreach($result AS $value){
						echo $value;
					}
				?>
			</table>
		</div>
		
	<?php
		unset($result);
		unset($userAccess);
	break;
	
	case "get_list_partners":
		$nalog_ids = $_POST['nalog_ids'];
		$nalogArray = array();
		$result = array();
		// var_dump($nalog_ids);
		// exit();
		if(is_array($nalog_ids)){
			//echo "Niz je";
			$nalogArray = $nalog_ids;
		}else{
			//echo "Nije niz";
			array_push($nalogArray, $nalog_ids);
		}
		$userAccess = getUserAccess($userId);
		if(in_array("0", $nalogArray) AND count($nalogArray) == 1){
			//Ovdje uzimam sve partnere za koje je user admin START
			$defaulNalog = $userAccess["superAdminAccess"]["nalog"][0];
			if(is_null($defaulNalog)){
				$defaulNalog = $userAccess["adminAccess"]["nalog"][0];
			}

			$adminCheckPosition = getPositionElementInArrayR($defaulNalog, $userAccess["adminAccess"]["nalog"]);
			// var_dump($adminCheckPosition);
			// exit();
			$adminPartnersCheckArray = array();
			if(count($adminCheckPosition) != 0){
				foreach($adminCheckPosition AS $countPositionValue){
					$partnerId = $userAccess["adminAccess"]["partner"][$countPositionValue];
					array_push($adminPartnersCheckArray, $partnerId);
					$cnt_candidates_by_partner = countCandidatesByPartner($partnerId);
					$pb_val = $cnt_candidates_by_partner['val'];
					$pb_max	= $cnt_candidates_by_partner['max'];
					if($pb_max != 0)
						$pb_procentage 	= (($pb_val/$pb_max)*100);
					else
						$pb_procentage = 0;
					$resultRow = '';
					
					$resultRow = '
						<tr class = "row_handle_partner_click" nalog_id = "'.$defaulNalog.'" partner_id = "'.$partnerId.'" company_enpal = "'.getCompanyAccessR($defaulNalog).'">
							<td class = "table_partner_icon_column ">
								<i class="fa fa-building-o table_partner_icon" aria-hidden="true"></i>
							</td>
							<td class = "table_text table_partner_my_company">'.getCompanyNameR(getCompanyForPartnerIdR($partnerId)).'</td>
							<td style = "width:30%">
								<div class="progress" style = "height: 0.5rem!important;background-color: #e9ecef!important;">
									<div class="progress-bar bg-success" role="progressbar" style = "width:'.$pb_procentage.'%;" aria-valuenow="'.$pb_val.'" aria-valuemin="0" aria-valuemax="'.$pb_max.'"></div>
								</div>
							</td>
							<td class = "text_color_secondary pl-1 text-align:center;" style = "width:10%;">
							'.$pb_val.'/'.$pb_max.'
							</td>
						</tr>
					';						
						
					if($defaulNalog == 222 AND $partnerId == 11){
						$resultRow = '';
					}
					else{
						array_push($result, $resultRow);
					}
				}
			}
			//Ovdje uzimam sve partnere za koje je user admin END 
			
			//Sad uzimam ostale partnere od tog naloga START
			// var_dump($defaulNalog);
			$partnersInNalog = getPartneriNalogaArrayIdR($defaulNalog);
			// var_dump($partnersInNalog);
			// var_dump(count($partnersInNalog));
			if(count($partnersInNalog)){
				foreach($partnersInNalog AS $partnersInId){
					if(!in_array($partnersInId, $adminPartnersCheckArray)){
						$cnt_candidates_by_partner = countCandidatesByPartner($partnersInId);
						$pb_val = $cnt_candidates_by_partner['val'];
						$pb_max	= $cnt_candidates_by_partner['max'];
						if($pb_max != 0)
							$pb_procentage 	= (($pb_val/$pb_max)*100);
						else
							$pb_procentage = 0;

						$resultRow = '';
				
						$resultRow = '
							<tr class = "row_handle_partner_click" nalog_id = "'.$defaulNalog.'" partner_id = "'.$partnersInId.'" company_enpal = "'.getCompanyAccessR($defaulNalog).'">
								<td class = "table_partner_icon_column ">
									<i class="fa fa-handshake-o table_partner_icon" aria-hidden="true"></i>
								</td>
								<td class = "table_text">'.getCompanyNameR(getCompanyForPartnerIdR($partnersInId)).'</td>
								<td style = "width:30%">
									<div class="progress" style = "height: 0.5rem!important;background-color: #e9ecef!important;">
										<div class="progress-bar bg-success" role="progressbar" style = "width:'.$pb_procentage.'%;" aria-valuenow="'.$pb_val.'" aria-valuemin="0" aria-valuemax="'.$pb_max.'"></div>
									</div>
								</td>
								<td class = "text_color_secondary pl-1 text-align:center;" style = "width:10%;">
								'.$pb_val.'/'.$pb_max.'
								</td>
							</tr>
						';
						if($defaulNalog == 222 AND $partnersInId == 11){
							$resultRow = '';
						}
						else{
							array_push($result, $resultRow);
						}
						// var_dump($result);
						// exit();
					}
				}
			}
			//Sad uzimam ostale partnere od tog naloga END 
		}else{
			foreach($nalogArray AS $nalogId){
				//if(count())
				$adminCheckPosition = getPositionElementInArrayR($nalogId, $userAccess["adminAccess"]["nalog"]);
				$adminPartnersCheckArray = array();
				if(count($adminCheckPosition) != 0){
					foreach($adminCheckPosition AS $countPositionValue){
						$partnerId = $userAccess["adminAccess"]["partner"][$countPositionValue];
						array_push($adminPartnersCheckArray, $partnerId);
						
						$cnt_candidates_by_partner = countCandidatesByPartner($partnerId);
						$pb_val = $cnt_candidates_by_partner['val'];
						$pb_max	= $cnt_candidates_by_partner['max'];
						if($pb_max != 0)
							$pb_procentage 	= (($pb_val/$pb_max)*100);
						else
							$pb_procentage = 0;
						
						$resultRow = '';
						
						$resultRow = '
							<tr class = "row_handle_partner_click" nalog_id = "'.$nalogId.'" partner_id = "'.$partnerId.'" company_enpal = "'.getCompanyAccessR($nalogId).'">
								<td class = "table_partner_icon_column ">
									<i class="fa fa-building-o table_partner_icon" aria-hidden="true"></i>
								</td>
								<td class = "table_text table_partner_my_company">'.getCompanyNameR(getCompanyForPartnerIdR($partnerId)).'</td>
								<td style = "width:30%">
									<div class="progress" style = "height: 0.5rem!important;background-color: #e9ecef!important;">
										<div class="progress-bar bg-success" role="progressbar" style = "width:'.$pb_procentage.'%;" aria-valuenow="'.$pb_val.'" aria-valuemin="0" aria-valuemax="'.$pb_max.'"></div>
									</div>
								</td>
								<td class = "text_color_secondary pl-1 text-align:center;" style = "width:10%;">
								'.$pb_val.'/'.$pb_max.'
								</td>
							</tr>
						';
						echo $result_row;
						if($nalogId == 222 AND $partnerId == 11){
							$resultRow = '';
						}
						else{
							array_push($result, $resultRow);
						}
						
					}
				}
				//Ovdje uzimam sve partnere za koje je user admin END 
				
				//Sad uzimam ostale partnere od tog naloga START
				$partnersInNalog = getPartneriNalogaArrayIdR($nalogId);
				if(count($partnersInNalog)){
					foreach($partnersInNalog AS $partnersInId){
						if(!in_array($partnersInId, $adminPartnersCheckArray)){
							$cnt_candidates_by_partner = countCandidatesByPartner($partnersInId);
							$pb_val = $cnt_candidates_by_partner['val'];
							$pb_max	= $cnt_candidates_by_partner['max'];
							if($pb_max != 0)
								$pb_procentage 	= (($pb_val/$pb_max)*100);
							else
								$pb_procentage = 0;
							
							$resultRow = '';
							$resultRow = '
								<tr class = "row_handle_partner_click" nalog_id = "'.$nalogId.'" partner_id = "'.$partnersInId.'" company_enpal = "'.getCompanyAccessR($nalogId).'">
									<td class = "table_partner_icon_column ">
										<i class="fa fa-handshake-o table_partner_icon" aria-hidden="true"></i>
									</td>
									<td class = "table_text">'.getCompanyNameR(getCompanyForPartnerIdR($partnersInId)).'</td>
									<td style = "width:30%">
										<div class="progress" style = "height: 0.5rem!important;background-color: #e9ecef!important;">
											<div class="progress-bar bg-success" role="progressbar"  style = "width:'.$pb_procentage.'%;" aria-valuenow="'.$pb_val.'" aria-valuemin="0" aria-valuemax="'.$pb_max.'"></div>
										</div>
									</td>
									<td class = "text_color_secondary pl-1 text-align:center;" style = "width:10%;">
									'.$pb_val.'/'.$pb_max.'
									</td>
								</tr>
							';
							if($nalogId == 222 AND $partnersInId == 11){
								$resultRow = '';
							}
							else{
								array_push($result, $resultRow);
							}
						}
					}
				}
				//Sad uzimam ostale partnere od tog naloga END 
			}
		}
		
	?>
		<p class = "text_color" style = "font-weight: 600; font-size: 18px; line-height: 22px; padding-bottom:10px;"><?php echo $txtArray['Partneri'][$languageUser]; ?>:</p>
		<div id = "box_partners" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); overflow: auto; height: 15rem;" data-bs-offset="0" tabindex="0">
			<table class = "table_text" style = "width:95%">
				<?php 
					foreach($result AS $value){
						echo $value;
					}
				?>
			</table>
		</div>
	<?php
		unset($nalogArray);
		unset($userAccess);
		unset($adminPartnersCheckArray);
		unset($partnersInNalog);
	break;
	
	case "get_list_candidates":
		if(isset($_REQUEST["bar_id"]))
			$bar_id 	= $_REQUEST["bar_id"];
		if(isset($_REQUEST["type"]))
			$type 		= $_REQUEST["type"];		
		if(isset($_REQUEST["nalog_id"]))
			$nalog_id	= $_REQUEST["nalog_id"];
		if(isset($_REQUEST["partner_id"]))
			$partner_id	= $_REQUEST["partner_id"];
		
		$user_type = getUserTypeR($userId);
		$flag_show_id = showCandidateIdR($userId);
		
		$add_rows = "";
		?>
		<div style = "display:none;" id = "to_append_export"></div>
		<div style = "display:none;" id = "to_append_enpal_export"></div>
		<div style = "display:none;" id = "to_append_interview_export"></div>
		<div class="modal fade" id="modalEditCandidatePartnerData" data-bs-keyboard="false" aria-labelledby="modalEditCandidatePartnerData" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content border-0">
					<div class="modal-header border-bottom-0 text-center">
						<h5 class="modal-title w-100" id="modalPartnerLabel"><?php echo $txtArray["Ažuriraj podatke"][$languageUser]; ?></h5>
						<!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
					</div>
					<div class="modal-body px-3">
						<div class = "row pt-1 my-3">
							<div class = "col-12 text-center">
								<div class="mb-3 mx-5 row">
									<div class="col-sm-12">
										<label style = "width:100%;text-align:left;"><?php echo $txtArray["Partner"][$languageUser]; ?>:</label>
										<select class="selectpicker form-control" id="editPartnerIDVal">
											<?php 
												$partneriNaloga = getPartneriNalogaArrayIdR($nalog_id);
												if(count($partneriNaloga) != 0){
													foreach($partneriNaloga AS $valPartnerId){
														$flag_write 	= true;
														if($valPartnerId == 11  AND $nalog_id == 222){
															$flag_write = false;
														}
														if($flag_write){
															echo '<option value = "'.$valPartnerId.'">'.getCompanyNameR(getCompanyForPartnerIdR($valPartnerId)).'</option>';												
														}
													}
												}
											?>
										</select>
										<script>
											$(document).ready(function() {
												$('#editPartnerIDVal').selectpicker();
											});
										</script>
									</div>
								</div>
								<div class="mb-3 mx-5 row">
									<div class="col-sm-12">
										<label style = "width:100%;text-align:left;">
										<?php 
											if(hasPartnersR($nalog_id))
												echo $txtArray["Lokacija Partnera"][$languageUser]; 
											else
												echo $txtArray["Mjesto"][$languageUser];
										?>
										</label>
										<select class="selectpicker form-control" id="editPartnerLocation">
											<?php
											getPartnerLocation($nalog_id);
											?>
											<option value = "0"><?php echo $txtArray["Ostalo"][$languageUser]; ?></option>
										</select>
										<script>
											$(document).ready(function() {
												$('#editPartnerLocation').selectpicker();
											});
										</script>
									</div>
								</div>
								<script>
									$(document).on("change","#editPartnerLocation",function() {
										var partnerLocation = $("#editPartnerLocation").val();
										
										if(partnerLocation == 0){
											$("#editHideShowLocOstalo").show();
										}else{
											$("#editHideShowLocOstalo").hide();
										}
									});
								</script>
								<div class="mb-3 mx-5 row" id = "editHideShowLocOstalo" style = "display:none;">
									<div class="col-sm-12">
										<input type = "text" class="form-control" id="editPartnerLocationOstalo" placeholder = "<?php echo $txtArray["Lokacija Partnera"][$languageUser]; ?>">
									</div>
								</div>
								<div class="mb-3 mx-5 row">
									<div class="col-sm-12">
										<label style = "width:100%;text-align:left;"><?php echo $txtArray["Radna pozicija"][$languageUser]; ?></label>
										<select class="selectpicker form-control" id="editPartnerPosition" >
										<?php
											getWorkingPosition($nalog_id);
										?>
											<option value = "0"><?php echo $txtArray["Ostalo"][$languageUser]; ?></option>
										<select/>
										<script>
											$(document).ready(function() {
												$('#editPartnerPosition').selectpicker();
											});
										</script>
									</div>
								</div>
								<script>
									$(document).on("change","#editPartnerPosition",function() {
										var partnerPosition = $("#editPartnerPosition").val();
										
										if(partnerPosition == 0){
											$("#editHideShowPositionOstalo").show();
										}else{
											$("#editHideShowPositionOstalo").hide();
										}
									});
								</script>
								<div class="mb-3 mx-5 row" id = "editHideShowPositionOstalo" style = "display:none;">
									<div class="col-sm-12">
										<input type = "text" class="form-control" id="editPartnerPositionOstalo" placeholder = "<?php echo $txtArray["Radna pozicija"][$languageUser]; ?>">
									</div>
								</div>
								<div class="mb-3 mx-5 row">
									<div class="col-sm-12">
										<label style = "width:100%;text-align:left;"><?php echo $txtArray["Iznos plate"][$languageUser]; ?></label>
										<input type="text" class="form-control" id="editPartnerSalary">
									</div>
								</div>
								<script>
									$('#editPartnerSalary').on('input', function() {
										this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
									});
								</script>
							</div>
						</div>
					</div>
					<div class="modal-footer justify-content-center border-top-0">
						<button class="btn btn-primary" id = "edit_candidate_partner_data" location = "l"><?php echo $txtArray["Ažuriraj"][$languageUser]; ?></button>
					</div>
					
				</div>
			</div>
		</div>
		<?php
		if($type == 1){
			$filter_termin  	= false;
			$filter_partner  	= false;
			$filter_pozicija 	= false;
			
			if($bar_id == 3){
				$list_type = $txtArray['Intervju'][$languageUser];
				$add_rows .= '<th class="text-center">'.$txtArray['Smjer'][$languageUser].'</th>';
				$add_rows .= '<th class="text-center">Termin</th>';	
			}
			if($bar_id == 3 || $bar_id == 4){
				$add_rows .= '<th class="text-center">'.$txtArray['Ocjena'][$languageUser].'</th>';	
			}
			if($bar_id == 4){
				$list_type = $txtArray['Prihvaćen'][$languageUser];
				$add_rows .= '<th class="text-center">'.$txtArray['Radna pozicija'][$languageUser].'</th>';
				$add_rows .= '<th class="text-center">'.$txtArray['Partner'][$languageUser].'</th>';
				$add_rows .= '<th class="text-center">Termin</th>';	
				$add_rows .= '<th class="text-center">'.$txtArray['Vrijeme prihvatanja'][$languageUser].'</th>';
				// if($nalog_id == 228){
					// $filter_pozicija 	= true;

				// }
					$filter_partner 	= true;
			}
			if($bar_id == 3){
				// $add_rows .= '<th class="text-center">'.$txtArray['Smjer'][$languageUser].'</th>';
				$add_rows .= '<th class="text-center">'.$txtArray['Akcija'][$languageUser].'</th>';
				$filter_termin = true;
			}
			
			if($bar_id == 5){
				$add_rows .= '<th class="text-center">'.$txtArray['Smjer'][$languageUser].'</th>';
				$add_rows .= '<th class="text-center">'.$txtArray['Razlog odbijanja'][$languageUser].'</th>';
				$add_rows .= '<th class="text-center">'.$txtArray['Vrijeme odbijanja'][$languageUser].'</th>';
				$list_type = $txtArray['Odbijen'][$languageUser];
			}
			
			?>	
			<div id = "to_append_to_modal_candidate_absent"></div>
			<div class = "col-12">
				<div class = "row align-items-center">
					<div class = "col-1 text-center goToDash" style = "cursor:pointer;" user_type = "1" back_to_type = "<?php echo $type; ?>" nalog_id = "<?php echo $nalog_id; ?>" partner_id = "0">
						<i class="fa fa-angle-left fa-3x" aria-hidden="true"></i>
					</div>
					<div class = "col-11 text-center">
						<p class = "text_color mb-0" style = "font-style: normal; font-weight: bold; font-size: 28px;"><?php echo $list_type; ?></p>
					</div>
				</div>
				<div class = "row mb-3">
					<div class = "col-md-12">
						<div class = "px-5 py-5" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
							<div class = "row">
								<div class = "col-12 row">
									<?php 
									if($filter_termin){
										$query_get_nalog_appointments = $db -> prepare("
											SELECT  pap_date, pap_city, pap_id
											FROM idk_pp_appointments
											WHERE pap_nalog_id IN (:nalog_id)
										");
										$query_get_nalog_appointments -> execute(array(':nalog_id' => $nalog_id));
										?>
										<select class="selectpicker mb-5 col-6 offset-4" id="select_appointment" multiple placeholder="<?php echo $txtArray['ODABERITETERMINE'][$languageUser]; ?>">
										<?php
										while($row_get_nalog_appointments = $query_get_nalog_appointments->fetch()){
											$pap_date 	= $row_get_nalog_appointments['pap_date'];
											$pap_city 	= $row_get_nalog_appointments['pap_city'];
											$pap_id 	= $row_get_nalog_appointments['pap_id'];
											?>
											<option value="<?php echo $pap_id; ?>"><?php echo $pap_city.' '.date('d.m.Y', strtotime($pap_date)); ?></option>
											<?php
										}
										?>
										</select>
										<?php
									}
									if($filter_partner){
										?>
									<select  class="selectpicker mb-5 col-6 offset-4" id="select_partner" multiple placeholder = "<?php echo $txtArray['Odaberi partnera'][$languageUser]; ?>">
										<?php
										$partneriNaloga = getPartneriNalogaArrayIdR($nalog_id);
										if(count($partneriNaloga) != 0){
											foreach($partneriNaloga AS $valPartnerId){
												$flag_write 	= true;
												if($valPartnerId == 11  AND $nalog_id == 222){
													$flag_write = false;
												}
												if($flag_write){
													echo '<option value = "'.$valPartnerId.'">'.getCompanyNameR(getCompanyForPartnerIdR($valPartnerId)).'</option>';												
												}
											}
										}
										?>
										</select>
										<?php
									}
									
									
									if($filter_pozicija){
										?>
										<select  class="selectpicker mb-5 col-6 offset-4" id="select_working_position" multiple placeholder = "<?php echo $txtArray["Radna pozicija"][$languageUser]; ?>">
										<?php
											getWorkingPosition($nalog_id);
										?>
										</select>
										<?php
									}
									?>
									<script>
									$(document).ready(function() {
										$('#select_appointment').selectpicker();
										$('#select_partner').selectpicker();
										$('#select_working_position').selectpicker();
									});
									</script>
									
									<div id = "candidates_list_table_container">
										<div class = "text-center">
											<button id = "btn_refresh_table" class = "click_refresh_table"><i class="fa fa-refresh" aria-hidden="true"></i></button>
										</div>
										<table id="table_list_from_projects" class="hover compact striped row-border" cellspacing="0" width="100%">
											<thead>
												<tr>
													<?php 
													if($flag_show_id)
														echo '<th class = "text-center">ID</th>';
													?>
													<th class="text-center"><?php echo $txtArray['Ime kandiadta'][$languageUser]; ?></th>
													<th class="text-center"><?php echo $txtArray['Jezik'][$languageUser]; ?></th>
													<th class="text-center"><?php echo $txtArray['Edukacija'][$languageUser]; ?></th>	
													<?php echo $add_rows; ?>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- ODBIJ LISTA -->
			<div class="modal fade" id="modalOdbij" aria-hidden="true" aria-labelledby="modalOdbijLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content border-0">
						<div class="modal-header border-bottom-0 text-center">
							<h5 class="modal-title w-100" id="modalOdbijLabel"><?php echo $txtArray["Odbij kandidata"][$languageUser]; ?></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class = "row pt-1 my-3">
								<div class = "col-12 text-center">
									<div class="mb-5 mx-5 row">
										<div class="col-sm-12">
											<div id = "razlogOdbijanjaIdEffect">
											<select class="selectpicker form-control" id="razlogOdbijanjaId" placeholder = "<?php echo $txtArray["Odaberite razlog odbijanja"][$languageUser]; ?>">
												<?php 
													$reject_resons = getRejectReasonsArrayR($languageUser);
													foreach($reject_resons["count"] AS $rrCnt){
														echo '<option value = "'.$reject_resons["id"][$rrCnt].'">'.$reject_resons["name"][$rrCnt].'</option>';
													}
													unset($reject_resons);
												?>
											</select>
											<script>
												$(document).ready(function() {
													$('#razlogOdbijanjaId').selectpicker();
												});
											</script>
											</div>
										</div>
									</div>
								</div>
								<div class = "col-12 text-center">
									<p  style = "color: #8E97A3; font-weight: 600; font-size:16px;" class = "mb-0">
										<?php echo $txtArray["Jeste li sigurni da odbijate ovog kandidata?"][$languageUser]; ?>
									</p>
								</div>
							</div>
						</div>
						<div class="modal-footer justify-content-center border-top-0">
							<button class="btn btn-danger" id = "reject_candidate"><?php echo $txtArray["Odbijam"][$languageUser]; ?></button>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="modalAccept" aria-hidden="true" aria-labelledby="modalAcceptLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content border-0">
						<div class="modal-header border-bottom-0 text-center">
							<h5 class="modal-title w-100" id="modalAcceptLabel"><?php echo $txtArray["Prihvati kandidata"][$languageUser]; ?></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class = "row pt-1 my-3">
								<div class = "col-12 text-center">
									<p  style = "color: #8E97A3; font-weight: 600; font-size:16px;" class = "mb-0">
										<?php echo $txtArray["Jeste li sigurni da prihvatate ovog kandidata?"][$languageUser]; ?>
									</p>
								</div>
							</div>
						</div>
						<div class="modal-footer justify-content-center border-top-0">
							<button class="btn btn-success" id = "hire_candidate" role="button"><?php echo $txtArray["Prihvatam"][$languageUser]; ?></button>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="modalPartner" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modalPartnerLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content border-0">
						<div class="modal-header border-bottom-0 text-center">
							<?php 
								if(hasPartnersR($nalog_id))
									echo '<h5 class="modal-title w-100" id="modalPartnerLabel">'.$txtArray["Dodijeli partneru"][$languageUser].'</h5>';
								else
									echo '<h5 class="modal-title w-100" id="modalPartnerLabel">'.$txtArray["Detalji ugovora"][$languageUser].'</h5>';
							?>
							<!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
						</div>
						<div class="modal-body px-3">
							<div class = "row pt-1 my-3">
								<div class = "col-12 text-center">
									<div class="mb-3 mx-5 row">
										<div class="col-sm-12">
											<select class="selectpicker form-control" id="partnerIDVal" placeholder = "<?php echo $txtArray["Partner"][$languageUser]; ?>">
												<?php 
													$partneriNaloga = getPartneriNalogaArrayIdR($nalog_id);
													if(count($partneriNaloga) != 0){
														foreach($partneriNaloga AS $valPartnerId){
															$flag_write 	= true;
															if($valPartnerId == 11  AND $nalog_id == 222){
																$flag_write = false;
															}
															if($flag_write){
																echo '<option value = "'.$valPartnerId.'">'.getCompanyNameR(getCompanyForPartnerIdR($valPartnerId)).'</option>';												
															}
														}
													}
												?>
											</select>
											<script>
												$(document).ready(function() {
													$('#partnerIDVal').selectpicker();
												});
											</script>
										</div>
									</div>
									<div class="mb-3 mx-5 row">
										<div class="col-sm-12">
											<select class="selectpicker form-control" id="partnerLocation" placeholder = "<?php 
												if(hasPartnersR($nalog_id))
													echo $txtArray["Lokacija Partnera"][$languageUser]; 
												else
													echo $txtArray["Mjesto"][$languageUser];
											?>">
												<?php
												getPartnerLocation($nalog_id);
												?>
												<option value = "0"><?php echo $txtArray["Ostalo"][$languageUser]; ?></option>
											</select>
											<script>
												$(document).ready(function() {
													$('#partnerLocation').selectpicker();
												});
											</script>
										</div>
									</div>
									<script>
										$(document).on("change","#partnerLocation",function() {
											var partnerLocation = $("#partnerLocation").val();
											
											if(partnerLocation == 0){
												$("#hideShowLocOstalo").show();
											}else{
												$("#hideShowLocOstalo").hide();
											}
										});
									</script>
									<div class="mb-3 mx-5 row" id = "hideShowLocOstalo" style = "display:none;">
										<div class="col-sm-12">
											<input type = "text" class="form-control" id="partnerLocationOstalo" placeholder = "<?php echo $txtArray["Lokacija Partnera"][$languageUser]; ?>">
										</div>
									</div>
									<div class="mb-3 mx-5 row">
										<div class="col-sm-12">
											<select class="selectpicker form-control" id="partnerPosition" placeholder = "<?php echo $txtArray["Radna pozicija"][$languageUser]; ?>">
											<?php
												getWorkingPosition($nalog_id);
											?>
												<option value = "0"><?php echo $txtArray["Ostalo"][$languageUser]; ?></option>
											<select/>
											<script>
												$(document).ready(function() {
													$('#partnerPosition').each(function(){
														$(this).selectpicker();
													});
												});
											</script>
										</div>
									</div>
									<script>
										$(document).on("change","#partnerPosition",function() {
											var partnerPosition = $("#partnerPosition").val();
											
											if(partnerPosition == 0){
												$("#hideShowPositionOstalo").show();
											}else{
												$("#hideShowPositionOstalo").hide();
											}
										});
									</script>
									<div class="mb-3 mx-5 row" id = "hideShowPositionOstalo" style = "display:none;">
										<div class="col-sm-12">
											<input type = "text" class="form-control" id="partnerPositionOstalo" placeholder = "<?php echo $txtArray["Radna pozicija"][$languageUser]; ?>">
										</div>
									</div>
									<div class="mb-3 mx-5 row">
										<div class="col-sm-12">
											<input type="text" class="form-control" id="partnerSalary"  placeholder = "<?php echo $txtArray["Iznos plate"][$languageUser]; ?>">
										</div>
									</div>
									<script>
										$('#partnerSalary').on('input', function() {
											this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
										});
									</script>
								</div>
							</div>
						</div>
						<div class="modal-footer justify-content-center border-top-0">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $txtArray["Kasnije"][$languageUser]; ?></button>
							<button class="btn btn-primary" id = "assign_candidate_to_partner" ><?php echo $txtArray["Upload"][$languageUser]; ?></button>
						</div>
						
					</div>
				</div>
			</div>
		<?php
		}
		else if($type == 2){
			$filter_pozicija 	= false;

			if($bar_id == 2){
				$add_rows .=  '<th class="text-center">'.$txtArray['Mogući početak rada'][$languageUser].'</th>';
				$add_rows .=  '<th class="text-center">'.$txtArray["Ažuriraj podatke"][$languageUser].'</th>';
				$add_rows .=  '<th class="text-center">'.$txtArray["Ugovor"][$languageUser] .'</th>';	

			}
			if($nalog_id == 228)
				$filter_pozicija 	= true;
			?>
			<div id = "to_append_to_modal_manage_documents"></div>
			<div class = "col-12">
				<div class = "row align-items-center">
					<div class = "col-1 text-center goToDash" style = "cursor:pointer;" user_type = "1" back_to_type = "<?php echo $type; ?>" nalog_id = "<?php echo $nalog_id; ?>" partner_id = "<?php echo $partner_id; ?>">
						<i class="fa fa-angle-left fa-3x" aria-hidden="true"></i>
					</div>
					<div class = "col-11 text-center">
						<p class = "text_color mb-0" style = "font-style: normal; font-weight: bold; font-size: 28px;"><?php echo $list_type; ?></p>
					</div>
				</div>
				<div class = "row mb-3">
					<div class = "col-md-12">
						<div class = "px-5 py-5" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
							<div class = "row">
								<div class = "col-12 row">
									<?php
									if($filter_pozicija){
										?>
										<select  class="selectpicker mb-5 col-6 offset-4" id="select_working_position" multiple placeholder = "<?php echo $txtArray["Radna pozicija"][$languageUser]; ?>">
										<?php
											getWorkingPosition($nalog_id);
										?>
										</select>
										<?php
									}
									?>
									<script>
										$(document).ready(function() {
											$('#select_working_position').selectpicker();
										});
									</script>
									<div id = "candidates_list_table_container">
										<div class = "text-center">
											<button id = "btn_refresh_table" class = "click_refresh_table"><i class="fa fa-refresh" aria-hidden="true"></i></button>
										</div>
										<table id="table_list_from_projects" class="hover compact striped row-border" cellspacing="0" width="100%">
											<thead>
												<tr>
													<?php 
													if($flag_show_id)
														echo '<th class = "text-center">ID</th>';
													?>
													<th class="text-center"><?php echo $txtArray['Ime kandiadta'][$languageUser]; ?></th>
													<th class="text-center"><?php echo $txtArray["Radna pozicija"][$languageUser]; ?></th>
													<th class="text-center"><?php echo $txtArray["Lokacija Partnera"][$languageUser]; ?></th>								
													<th class="text-center"><?php echo $txtArray["Iznos plate"][$languageUser]; ?></th>	
													<?php echo $add_rows; ?>
													
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		else if($type == 3 AND $bar_id  < 4){
			?>
			<div class = "col-12">
				<div class = "row align-items-center">
					<div class = "col-1 text-center goToDash" style = "cursor:pointer;" user_type = "1" back_to_type = "<?php echo $type; ?>" nalog_id = "<?php echo $nalog_id; ?>" partner_id = "<?php echo $partner_id; ?>">
						<i class="fa fa-angle-left fa-3x" aria-hidden="true"></i>
					</div>
					<div class = "col-11 text-center">
						<p class = "text_color mb-0" style = "font-style: normal; font-weight: bold; font-size: 28px;"><?php echo $list_type; ?></p>
					</div>
				</div>
				<div class = "row mb-3">
					<div class = "col-md-12">
						<div class = "px-5 py-5" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
							<div class = "row">
								<div class = "col-12 row">
									<div id = "candidates_list_table_container">
										<div class = "text-center">
											<button id = "btn_refresh_table" class = "click_refresh_table"><i class="fa fa-refresh" aria-hidden="true"></i></button>
										</div>
										<table id="table_list_from_projects" class="hover compact striped row-border" cellspacing="0" width="100%">
											<thead>
												<tr>
													<?php 
													if($flag_show_id)
														echo '<th class = "text-center">ID</th>';
													?>
													<th class="text-center"><?php echo $txtArray['Ime kandiadta'][$languageUser]; ?></th>
													<th class="text-center"><?php echo $txtArray["Status nostrifikacije"][$languageUser]; ?></th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		else if($type == 3 AND $bar_id < 8){
			$add_columns = "";
			// if($bar_id == 2){
			// 	$add_columns .= '<th class = "text-center">'.$txtArray["Zakazan termin n"][$languageUser].'</th>';
			// 	$add_columns .= '<th class = "text-center">'.$txtArray["Datum termina"][$languageUser].'</th>';
			// }
			?>
			<div class = "col-12">
				<div class = "row align-items-center">
					<div class = "col-1 text-center goToDash" style = "cursor:pointer;" user_type = "1" back_to_type = "<?php echo $type; ?>" nalog_id = "<?php echo $nalog_id; ?>" partner_id = "<?php echo $partner_id; ?>">
						<i class="fa fa-angle-left fa-3x" aria-hidden="true"></i>
					</div>
					<div class = "col-11 text-center">
						<p class = "text_color mb-0" style = "font-style: normal; font-weight: bold; font-size: 28px;"><?php echo $list_type; ?></p>
					</div>
				</div>
				<div class = "row mb-3">
					<div class = "col-md-12">
						<div class = "px-5 py-5" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
							<div class = "row">
								<div class = "col-12 row">
									<div id = "candidates_list_table_container">
										<div class = "text-center">
											<button id = "btn_refresh_table" class = "click_refresh_table"><i class="fa fa-refresh" aria-hidden="true"></i></button>
										</div>
										<table id="table_list_from_projects" class="hover compact striped row-border" cellspacing="0" width="100%">
											<thead>
												<tr>
													<?php 

													if($flag_show_id)
														echo '<th class = "text-center">ID</th>';
													?>
													<th class="text-center"><?php echo $txtArray["Ime kandiadta"][$languageUser]; ?></th>
													<th class="text-center"><?php echo $txtArray["Nivo"][$languageUser];?></th>
													<th class="text-center"><?php echo "Status"; ?></th>
													<th class="text-center"><?php echo $txtArray["Dana na statusu"][$languageUser]; ?></th>
													<?php
														echo $add_columns;
													?>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		else if($type == 3 AND $bar_id == 8){
			?>
			<div class = "col-12">
				<div class = "row align-items-center">
					<div class = "col-1 text-center goToDash" style = "cursor:pointer;" user_type = "1" back_to_type = "<?php echo $type; ?>" nalog_id = "<?php echo $nalog_id; ?>" partner_id = "<?php echo $partner_id; ?>">
						<i class="fa fa-angle-left fa-3x" aria-hidden="true"></i>
					</div>
					<div class = "col-11 text-center">
						<p class = "text_color mb-0" style = "font-style: normal; font-weight: bold; font-size: 28px;"><?php echo $list_type; ?></p>
					</div>
				</div>
				<div class = "row mb-3">
					<div class = "col-md-12">
						<div class = "px-5 py-5" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
							<div class = "row">
								<div class = "col-12 row">
									<div id = "candidates_list_table_container">
										<div class = "text-center">
											<button id = "btn_refresh_table" class = "click_refresh_table"><i class="fa fa-refresh" aria-hidden="true"></i></button>
										</div>
										<table id="table_list_from_projects" class="hover compact striped row-border" cellspacing="0" width="100%">
											<thead>
												<tr>
													<?php 

													if($flag_show_id)
														echo '<th class = "text-center">ID</th>';
													?>
													<th class="text-center"><?php echo $txtArray['Ime kandiadta'][$languageUser]; ?></th>
													<th class="text-center"><?php echo "Nivo" ?></th>
													<th class="text-center"><?php echo "Status" ?></th>
													<th class="text-center"><?php echo $txtArray["Dana na statusu"][$languageUser]; ?></th>
													<th class="text-center"><?php echo $txtArray["Status nostrifikacije"][$languageUser]; ?></th>
													<?php
														echo $add_columns;
													?>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		else if($type == 4){
			$add_columns = "";
			if($bar_id != 4){
				$add_columns .= '<th class="text-center">Status</th>';
			}
			if($bar_id == 1){
				$add_columns .= '<th class = "text-center">'.$txtArray["Rok predaje"][$languageUser].'</th>';
				$add_columns .= '<th class="text-center" style = "width:20%">'.$txtArray["Progres dokumenata"][$languageUser].'</th>';
			}
			else if($bar_id == 2){
				$add_columns .= '<th class = "text-center">'.$txtArray["Zakazan termin na"][$languageUser].'</th>';
				$add_columns .= '<th class = "text-center">'.$txtArray["Datum termina"][$languageUser].'</th>';
			}
			else if($bar_id == 3){
				$add_columns .= '<th class = "text-center">'.$txtArray["Datum isteka vize"][$languageUser].'</th>';
			}
			?>
			<div class = "col-12">
				<div class = "row align-items-center">
					<div class = "col-1 text-center goToDash" style = "cursor:pointer;" user_type = "1" back_to_type = "<?php echo $type; ?>" nalog_id = "<?php echo $nalog_id; ?>" partner_id = "<?php echo $partner_id; ?>">
						<i class="fa fa-angle-left fa-3x" aria-hidden="true"></i>
					</div>
					<div class = "col-11 text-center">
						<p class = "text_color mb-0" style = "font-style: normal; font-weight: bold; font-size: 28px;"><?php echo $list_type; ?></p>
					</div>
				</div>
				<div class = "row mb-3">
					<div class = "col-md-12">
						<div class = "px-5 py-5" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
							<div class = "row">
								<div class = "col-12 row">
									<div id = "candidates_list_table_container">
										<div class = "text-center">
											<button id = "btn_refresh_table" class = "click_refresh_table"><i class="fa fa-refresh" aria-hidden="true"></i></button>
										</div>
										<table id="table_list_from_projects" class="hover compact striped row-border" cellspacing="0" width="100%">
											<thead>
												<tr>
													<?php 
													if($flag_show_id)
														echo '<th class = "text-center">ID</th>';
													?>
													<th class="text-center"><?php echo $txtArray['Ime kandiadta'][$languageUser]; ?></th>
													<th class="text-center"><?php echo $txtArray["Dana na statusu"][$languageUser]; ?></th>
													<?php
														echo $add_columns;
													?>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	break;

	case "reject_candidate":
		$kandidat_id		= $_REQUEST['kandidat_id'];
		$kandidat_id 		= getCandidateIdByKey($kandidat_id);
		$nalog_id 			= $_REQUEST['nalog_id'];
		$razlog 			= NULL;									//$_REQUEST['razlog_odbijanja'];
		$razlog_id 			= $_REQUEST['razlog_odbijanja_id'];
		$nalog_termin_id 	= $_REQUEST['nalog_termin_id'];
		
		//Arhiviranje termina kod kandidata za nalog START
			archiveCandidateAppointment($kandidat_id, $nalog_id); 
		//Arhiviranje termina kod kandidata za nalog END 
		
		$query_get_project_id_from = $db -> prepare("
			SELECT pk_id, pk_projectid 
			FROM idk_project_kandidati pk
			JOIN idk_projects pr 
			ON pr.project_id = pk.pk_projectid
			WHERE pr.project_name LIKE ('%Intervju%')
			AND pk.pk_kandidatid = :kandidat_id
			AND pr.project_nalogid = :nalog_id
		");
		
		$query_get_project_id_from -> execute(array(
			':nalog_id' => $nalog_id,
			':kandidat_id' => $kandidat_id
		));

		$row_get_project_id_from = $query_get_project_id_from -> fetch();
		$project_id_from 			= $row_get_project_id_from['pk_projectid'];
		$project_candidate_id_from 	= $row_get_project_id_from['pk_id'];
		
		$query_get_project_id_to = $db -> prepare("
			SELECT project_id
			FROM idk_projects
			WHERE project_nalogid = :nalog_id
			AND project_name LIKE ('%Odbijen%')
		");
		
		$query_get_project_id_to -> execute(array(':nalog_id' => $nalog_id));
		
		$row_get_project_id_to = $query_get_project_id_to -> fetch();
		
		$project_id_to = $row_get_project_id_to['project_id'];
		
		$upd_status_prijave = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = 5, kandidat_pp_razlog_odbijanja = '$razlog' WHERE kandidat_id = $kandidat_id");
		$upd_status_prijave->execute();
		
		$del_project = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_id = $project_candidate_id_from");
		$del_project->execute();
		
		$query = $db->prepare("
					INSERT INTO idk_project_kandidati
						(pk_projectid, pk_kandidatid)
					VALUES
						(:pk_projectid, :pk_kandidatid)");
		
		$query_insert_reason = $db->prepare("
			INSERT INTO 
				idk_kandidati_reject_reasons
				(
					krr_reason_id,
					krr_candidate_id,
					krr_nalog_id,
					krr_partner_id,
					krr_apointment_id,
					krr_description,
					krr_source,
					krr_done_by,
					krr_last_status
				)
			VALUES
				(
					:reason_id,
					:candidate_id,
					:nalog_id,
					:partner_id,
					:appointment_id,
					:description,
					:source,
					:done_by,
					:last_status
				)
		");
		$query_insert_reason->execute(array(
			":reason_id"		=> $razlog_id,
			":candidate_id" 	=> $kandidat_id,
			":nalog_id" 		=> $nalog_id,
			":partner_id" 		=> NULL,
			":appointment_id" 	=> $nalog_termin_id,
			":description" 		=> $razlog,
			":source" 			=> 2,
			":done_by" 			=> $userId,
			":last_status" 		=> 3
		));

		$log_date = date('Y-m-d H:i:s');
		$query->execute(array(
						':pk_projectid' => $project_id_to,
						':pk_kandidatid' => $kandidat_id
		));
		addToLogsStatusPrijave($project_id_from, $project_id_to, 5, $kandidat_id, 2);
		$log_desc = "Odbio kandidata [".$kandidat_id."] iz naloga ".$nalog_id;
		addToLogs($log_desc);
		// echo "kandidateid ".$kandidat_id." p from: ".$project_id_from." pk from ".$project_candidate_id_from." p to ".$project_id_to;
	break;

	case "hire_candidate":
		$kandidat_id	= $_REQUEST['kandidat_id'];
		$nalog_id 		= $_REQUEST['nalog_id'];
		$kandidat_id 	= getCandidateIdByKey($kandidat_id);
		//Arhiviranje termina kod kandidata za nalog START
			archiveCandidateAppointment($kandidat_id, $nalog_id); 
		//Arhiviranje termina kod kandidata za nalog END
		
		/*
			AUTOMATSKO VEZIVANJE KANDIDATA ZA PARTNERA START 
			*/
				$partner_result = hasPartnersR($nalog_id); 
				$partner_id = null; // kandidat_ppa_partner_id default null

				if ( $partner_result != 101 AND $partner_result != 102 AND $partner_result == 0 ) {

					/*
						Dakle ako funkcija vrati rezultat 0 i ako nije vraćen rezultat greske (101, 102)
						Onda je potrebno naci partnera naloga
					*/

					$partner_array = getPartneriNalogaArrayIdR($nalog_id);

					if ( count($partner_array) == 1 ) {

						/* 
							Funkcija vraća array
							Iz sigurnosti provjeravamo da li je vratila samo jedan element
							Ako je oznaceno da je nalog bez grupacija - onda ova funkcija mora vratiti samo jedan element
							Iz sigurnosti postavljen je count
						*/

						$partner_id = $partner_array[0];

						/*
							Update se izvrsava na query-u: $upd_status_prijave
						*/

					}

					unset($partner_array);
				}

			/*
			AUTOMATSKO VEZIVANJE KANDIDATA ZA PARTNERA END 
		*/

		$query_get_project_id_from = $db -> prepare("
			SELECT pk_id, pk_projectid 
			FROM idk_project_kandidati pk
			JOIN idk_projects pr 
			ON pr.project_id = pk.pk_projectid
			WHERE pr.project_name LIKE ('%Intervju%')
			AND pk.pk_kandidatid = :kandidat_id
			AND pr.project_nalogid = :nalog_id
		");
		
		$query_get_project_id_from -> execute(array(
			':nalog_id' => $nalog_id,
			':kandidat_id' => $kandidat_id
		));

		$row_get_project_id_from = $query_get_project_id_from -> fetch();
		$project_id_from 			= $row_get_project_id_from['pk_projectid'];
		$project_candidate_id_from 	= $row_get_project_id_from['pk_id'];
		
		$query_get_project_id_to = $db -> prepare("
			SELECT project_id
			FROM idk_projects
			WHERE project_nalogid = :nalog_id
			AND project_name LIKE ('%Ugovor%')
		");
		
		$query_get_project_id_to -> execute(array(':nalog_id' => $nalog_id));
		
		$row_get_project_id_to = $query_get_project_id_to -> fetch();
		
		$project_id_to = $row_get_project_id_to['project_id'];
		
		$upd_status_prijave = $db->prepare("
			UPDATE 
				idk_kandidati 
			SET 
				kandidat_status_prijave = 7,
				kandidat_pp_datum_prihvatanja = CURRENT_DATE, 
				kandidat_ppa_partner_id = :kandidat_ppa_partner_id
			WHERE 
				kandidat_id = :kandidat_id
		");
		$upd_status_prijave->execute(array(
			':kandidat_ppa_partner_id' => $partner_id, 
			':kandidat_id' => $kandidat_id
		));
		
		$del_project = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_id = $project_candidate_id_from");
		$del_project->execute();
		
		$query = $db->prepare("
					INSERT INTO idk_project_kandidati
						(pk_projectid, pk_kandidatid)
					VALUES
						(:pk_projectid, :pk_kandidatid)");

		$query -> execute(array(
			':pk_projectid' 	=> $project_id_to,
			':pk_kandidatid' 	=> $kandidat_id
		));
		addToLogsStatusPrijave($project_id_from, $project_id_to, 7, $kandidat_id, 2);
	break;
	
	case "assign_candidate_to_partner":

		$partnerIDVal			= $_REQUEST['partnerIDVal'];
		$partnerLocation		= $_REQUEST['partnerLocation'];
		$partnerLocationOstalo	= $_REQUEST['partnerLocationOstalo'];
		$partnerPosition		= $_REQUEST['partnerPosition'];
		$partnerPositionOstalo	= $_REQUEST['partnerPositionOstalo'];
		$partnerSalary			= $_REQUEST['partnerSalary'];
		$candidate_id			= $_REQUEST['candidate_id'];
		$nalog_id				= $_REQUEST['nalog_id'];
		$candidate_id 			= getCandidateIdByKey($candidate_id);

		if($partnerLocation == "0"){
			$partnerLocation = $partnerLocationOstalo;
		}
		if($partnerPosition == "0"){
			$partnerPosition = $partnerPositionOstalo;
		}

		$query_update_candidate = $db -> prepare("
			UPDATE idk_kandidati
			SET 
				kandidat_nalog_id = :nalog_id,
				kandidat_ppa_partner_id = :partnerIDVal,
				kandidat_pp_lokacija = :partnerLocation,
				kandidat_pp_pozicija = :partnerPosition,
				kandidat_pp_plata = :partnerSalary,
				kandidat_pp_povezao_user_id = :userId
			WHERE 
				kandidat_id = :candidate_id
		");
		
		$query_update_candidate -> execute(array(
			':nalog_id' 		=> $nalog_id,
			':partnerIDVal' 	=> $partnerIDVal,
			':partnerLocation' 	=> $partnerLocation,
			':partnerPosition' 	=> $partnerPosition,
			':partnerSalary' 	=> $partnerSalary,
			':userId' 			=> $userId,
			':candidate_id' 	=> $candidate_id
			
		));
		$log_desc = "Jobstep PP APP - Kandidat [".$candidate_id."] povezan za partnera: ".$partnerIDVal;
		addToLogs($log_desc);
	break;
	
	case "candidateProfile":
		$kandidat_key = $_POST["kandidat_id"];
		$nalog_id = $_POST["nalog_id"];
		$kandidat_id = intval(getCandidateIdByKey($kandidat_key));
		//**************************************************************************************
		//Funkcija Nalog za pristup profilu tog kandidata - tako rečeno START 		************
			$nalogForAccess = getNalogForAccess($kandidat_id, $nalog_id);
			$candidateNalog = intval($nalogForAccess["nalogId"][0]);
			$flagAccess = intval($nalogForAccess["flag"][0]);
			unset($nalogForAccess);
		//Funkcija Nalog za pristup profilu tog kandidata - tako rečeno END   		************
		//**************************************************************************************
		
		/*
			FUNKCIJA ZA PROVJERU DA LI SE RADI O ENPAL KOMPANIJI START
			*/
				$flagEnpalAccess = getCompanyAccessR($candidateNalog);
				//echo $flagEnpalAccess;
				/*
					Response: 
						1 - radi se enpal-u
						0 - nije enpal
				*/
			/*
			FUNKCIJA ZA PROVJERU DA LI SE RADI O ENPAL KOMPANIJI END	
		*/

		//**************************************************************************************
		//Novi nacin za bar id i type START 										************
			if($flagAccess == 1){
				$type = 1;
				$bar_id = 5;
			}else{
				$checkBarIdAndType = checkBarIdForCandidatArrayR($kandidat_id, $candidateNalog);
				$type = intval($checkBarIdAndType["type"][0]);
				$bar_id = intval($checkBarIdAndType["barId"][0]);
				unset($checkBarIdAndType);
			}
		//Novi nacin za bar id i type END 											************
		//**************************************************************************************
		
		/*
		//**************************************************************************************
		//Stari nacin za bar id i type START 										************
			$type = intval($_POST["type"]);
			$bar_id = intval($_POST["bar_id"]);
		//Stari nacin za bar id i type END  										************
		//**************************************************************************************
		*/

		//**************************************************************************************
		//Provjera da li user Može vidjeti id kandidata START 						************
			$checkId = showCandidateIdR($userId);
			if($checkId == 1){
				$idCandidatShow = "#".$kandidat_id;
			}else{
				$idCandidatShow = "";
			}
		//Provjera da li user Može vidjeti id kandidata END  						************
		//**************************************************************************************
	?>
		<div id = "to_append_to_modal_manage_documents"></div>
		<div class = "row">
			<div class = "col-12">
				<div class = "row">
					<div class = "col-12 text-center">
						<p class = "text_color" style = "font-style: normal; font-weight: bold; font-size: 28px; line-height: 34px;"><?php echo $txtArray["Profil kandidata"][$languageUser]." ".$idCandidatShow; ?></p>
					</div>
				</div>
				<div class = "row gx-5 gy-3">
					<?php 
						$queryInfoCandidate = $db->prepare("
							SELECT 
								kandidat_ime, 
								kandidat_prezime,
								kandidat_adresa,
								kandidat_grad, 
								kandidat_pbroj, 
								kandidat_drzava, 
								kandidat_slika, 
								kandidat_potencijalni_pocetak_rada,
								kandidat_email,
								kandidat_mobitel,
								kandidat_datumrodjenja,
								kandidat_ppa_partner_id,
								kandidat_pp_lokacija,
								kandidat_pp_pozicija,
								kandidat_pp_plata,
								kandidat_status_prijave, 
								kandidat_bracno_stanje,
								kandidat_drzavljanstvo_vrsta,
								kandidat_iskustvo_u_struci,
								kandidat_iskustvo_u_struci_trajanje,
								kandidat_zeljena_regija,
								kandidat_zeljeni_grad,
								kandidat_nacin_odlaska,
								povezan_na_dipl,
								kandidat_dipl_id
							FROM 
								idk_kandidati
							WHERE 
								kandidat_id = :kandidat_id
						");
						$queryInfoCandidate->execute(array(
							':kandidat_id' => $kandidat_id
						));
						$cntInfoCandidate = $queryInfoCandidate->rowCount();
						$rowInfoCandidate = $queryInfoCandidate->fetch();
						$candidateName = $rowInfoCandidate["kandidat_ime"];
						$candidateLName = $rowInfoCandidate["kandidat_prezime"];
						$candidateAddress = $rowInfoCandidate["kandidat_adresa"];
						$candidateCity = $rowInfoCandidate["kandidat_grad"];
						$candidateZipCode = $rowInfoCandidate["kandidat_pbroj"];
						$candidateState = $rowInfoCandidate["kandidat_drzava"];
						$candidateMail = $rowInfoCandidate["kandidat_email"];
						$candidateTel = $rowInfoCandidate["kandidat_mobitel"];
						$candidatePocetakRada = $rowInfoCandidate["kandidat_potencijalni_pocetak_rada"];
						$candidatePartnerId = $rowInfoCandidate["kandidat_ppa_partner_id"];
						$kandidat_pp_lokacija = $rowInfoCandidate["kandidat_pp_lokacija"];
						$kandidat_pp_pozicija = $rowInfoCandidate["kandidat_pp_pozicija"];
						$kandidat_pp_plata = $rowInfoCandidate["kandidat_pp_plata"];
						$kandidat_status_prijave = $rowInfoCandidate["kandidat_status_prijave"];
						if($rowInfoCandidate["kandidat_datumrodjenja"] != NULL){
							$candidateDatumRodjenja = date("d.m.Y",strtotime($rowInfoCandidate["kandidat_datumrodjenja"]));
						}else{
							$candidateDatumRodjenja = '<span class="badge bg-warning text-dark">'.$txtArray["Nema informacije"][$languageUser].'</span>';
						}
						if($rowInfoCandidate["kandidat_slika"] == 'none' OR $rowInfoCandidate["kandidat_slika"] == 'none.jpg'){
							$candidateImage = getSiteUrlr()."images/noImage.svg";
						}else{
							$candidateImage = getCRMUrlr()."/files/kandidati/".$rowInfoCandidate["kandidat_slika"]; 
						}
						$kandidat_bracno_stanje = intval($rowInfoCandidate["kandidat_bracno_stanje"]); 
						$kandidat_drzavljanstvo_vrsta = $rowInfoCandidate["kandidat_drzavljanstvo_vrsta"]; 
						$kandidat_iskustvo_u_struci = $rowInfoCandidate["kandidat_iskustvo_u_struci"]; 
						$kandidat_iskustvo_u_struci_trajanje = $rowInfoCandidate["kandidat_iskustvo_u_struci_trajanje"]; 
						$kandidat_zeljena_regija = $rowInfoCandidate["kandidat_zeljena_regija"]; 
						$kandidat_zeljeni_grad = $rowInfoCandidate["kandidat_zeljeni_grad"]; 
						$kandidat_nacin_odlaska = $rowInfoCandidate["kandidat_nacin_odlaska"]; 
						$povezan_na_dipl = $rowInfoCandidate["povezan_na_dipl"]; 
						$kandidat_dipl_id  = $rowInfoCandidate["kandidat_dipl_id"];
						
						if($povezan_na_dipl == 1 AND $kandidat_dipl_id != NULL){
							// pokupi statuse za dipl
							$status_dipla = getDIPLStatusByCandId($kandidat_dipl_id);
							$dipl_ustanova_name = getDIPLInstitutionName($kandidat_dipl_id);
							$status_dipla_text = getDIPLStatusOutput($status_dipla, $dipl_ustanova_name);
						}
						
					?>
					<div class = "col-md-4">
						<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
							<div class = "row pt-5">
								<div class = "col-12 text-center">
									<img src="<?php echo $candidateImage; ?>" class="rounded mx-auto d-block w-50" style = "box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.12); border: 1px solid #000000;" alt="...">
								</div>
							</div>
							<div class = "row pt-3">
								<div class = "col-12">
									<script>
										$(document).ready(function(){
											var kandidat_id = parseInt("<?php echo $kandidat_id; ?>");
											var nalogId = parseInt("<?php echo $candidateNalog; ?>");
											//$("#avgRating").html("");
											$.ajax({
												url: 'ajax.php?action=candidatMaxAvgRating',
												type: 'POST',
												data: {
													'kandidatId':kandidat_id,
													'nalogId':nalogId
												},
												dataType: 'html',
												success: function(data){
													if(data != ""){
													$(".maxAvgRating").html(data);
													$( ".maxAvgRating" ).show( 'slide', 10);
													}
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
										});
									</script>
									<div class = "maxAvgRating">
										
									</div>
								</div>
							</div>
							<?php 
								$checkInfoFlag = 0;
								if($type == 1){
									if($bar_id == 4){
										$checkInfoFlag = 1;
									}else{
										$checkInfoFlag = 0;
									}
								}else{
									$checkInfoFlag = 1;
								}
							?>
							<div class = "row pt-3">
								<div class = "col-12 text-center">
									<ul class=" list-group">
										<li class="list-group-item border-0" style = "padding: 0.1rem 1rem !important;">
											<p class = "text_color mb-0" style = "color: #4B4B4B; font-style: normal; font-weight: bold; font-size: 28px; line-height: 34px;"><?php echo $candidateName." ".$candidateLName; ?></p>
										</li>
										<!--<li class="list-group-item border-0" style = "padding: 0.1rem 1rem !important;">
											<p class = "text_color mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;">Zdravstveni radnik</p>
										</li>-->
										<?php 
											if($checkInfoFlag == 1){
										?>
										<li class="list-group-item border-0" style = "padding: 0.1rem 1rem !important;">
											<p class = "text_color pt-2 mb-0" style = "color: #8E97A3; font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;"><?php echo $candidateMail; ?></p>
										</li>
										<li class="list-group-item border-0" style = "padding: 0.1rem 1rem !important;">
											<p class = "text_color pb-2 mb-0" style = "color: #8E97A3; font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;"><?php echo $candidateTel; ?></p>
										</li>
										<li class="list-group-item border-0" style = "padding: 0.1rem 1rem !important;">
											<p class = "text_color mb-0" style = "color: #8E97A3; font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;"><?php echo $candidateAddress; ?></p>
										</li>
										<li class="list-group-item border-0" style = "padding: 0.1rem 1rem !important;">
											<p class = "text_color mb-0" style = "color: #8E97A3; font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;"><?php echo $candidateZipCode.", ".$candidateCity; ?></p>
										</li>
										<li class="list-group-item border-0" style = "padding: 0.1rem 1rem !important;">
											<p class = "text_color mb-0" style = "color: #8E97A3; font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;"><?php echo $candidateState; ?></p>
										</li>
										<?php 
											}
										?>
									</ul>
								</div>
							</div>
							
							<div class = "row pt-3">
								<div class = "col-12">
									<p style = "color: #B0B4B7 ;font-style: normal; font-weight: normal; font-size: 16px; line-height: 19px;"><?php echo $txtArray["Datum rođenja"][$languageUser]; ?></p>
									<div class = "row">
										<div class = "col-12">
											<p  style = "color: #4B4B4B ;font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;">
												<?php echo $candidateDatumRodjenja; ?>
											</p>
										</div>
									</div>
								</div>
							</div>
							<?php 
								if ($kandidat_bracno_stanje != 0){

									$text_bracno_stanje = "";

									if ($kandidat_bracno_stanje == 1){
										$text_bracno_stanje = $txtArray["Neoženjen/Neudana"][$languageUser];
									} else if ($kandidat_bracno_stanje == 2){
										$text_bracno_stanje = $txtArray["Oženjen/Udana"][$languageUser];
									} else if ($kandidat_bracno_stanje == 3) {
										$text_bracno_stanje = $txtArray["Udovac/Udovica"][$languageUser];
									} else {
										$text_bracno_stanje = $txtArray["Razveden/Razvedena"][$languageUser];
									}

									?>
										<div class = "row pt-3">
											<div class = "col-12">
												<p style = "color: #B0B4B7 ;font-style: normal; font-weight: normal; font-size: 16px; line-height: 19px;"><?php echo $txtArray["Bračno stanje"][$languageUser]; ?></p>
												<div class = "row">
													<div class = "col-12">
														<p  style = "color: #4B4B4B ;font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;">
															<?php 
																echo $text_bracno_stanje; 
															?>
														</p>
													</div>
												</div>
											</div>
										</div>
									<?php 
								}
							?>
							<div class = "row pt-3">
								<div class = "col-12">
									<p style = "color: #B0B4B7 ;font-style: normal; font-weight: normal; font-size: 16px; line-height: 19px;"><?php echo $txtArray["Jezik"][$languageUser]; ?></p>
									<?php
										$queryInfoLanguage = $db->prepare("SELECT
											kj_naziv,
											-- Nakon sortiranja, A0 vraćamo na Bez znanja
											IF(kj_slusanje = 'A0','Bez znanja',kj_slusanje) AS kj_slusanje
											FROM
												(
												SELECT 	cvl_id,
														kj_naziv,
														-- Potrebno zbog sortiranja, jer je Bez znanja najniži jezik
														IF(`idk_kandidat_jezici`.`kj_slusanje` = 'Bez znanja', 'A0', `idk_kandidat_jezici`.`kj_slusanje`) AS kj_slusanje
												FROM
													`idk_kandidat_jezici`
												LEFT JOIN `idk_candidate_verified_languages` ON `idk_kandidat_jezici`.`kj_id` = `idk_candidate_verified_languages`.`cvl_id`
												WHERE
													`idk_kandidat_jezici`.`kj_kandidatid` = :kj_kandidatid
												AND
													`idk_kandidat_jezici`.`kj_naziv` LIKE 'Njemacki'
												ORDER BY
													-- Sortirati prvo po cvl_id, tako da će potvrđen jezik
													-- uvijek biti prvi
													cvl_id 		DESC,
													kj_slusanje DESC
											) AS subq
										");
										$queryInfoLanguage->execute(array(
											':kj_kandidatid' => $kandidat_id
										));

										$languages = $queryInfoLanguage->fetchAll(PDO::FETCH_ASSOC);

										if(count($languages) > 0){
											// Redovi sa jezicima su već sortirani, prvi jezik je ili 
											// potvrđen, ili najveći od nepotvrđenih
											$rowInfoLanguage = $languages[0];

											$kj_naziv = $rowInfoLanguage["kj_naziv"]; 
											$kj_naziv = $txtArray[$kj_naziv][$languageUser];
											$kj_slusanje = $rowInfoLanguage["kj_slusanje"];
											if($kj_slusanje == "Bez znanja"){
												$kj_slusanje_ispis = $txtArray["Bez znanja"][$languageUser];
											}else{
												$kj_slusanje_ispis = $kj_slusanje;
											}
											$styleLang = getProvjeraJezikaKriterij($kj_slusanje, $candidateNalog);
											$slusanje_bg = $styleLang[0];
											$slusanje_style = $styleLang[1];
											$slusanje_value = $styleLang[2];
									?>
											<div class = "row mb-3">
												<div class = "col-6">
													<p  style = "color: #4B4B4B ;font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;"><?php echo $kj_naziv; ?></p>
												</div>
												<div class = "col-6 ">
													<p class = "text-right" style = "color: #8E97A3 ;font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;"><?php echo $kj_slusanje_ispis; ?></p>
												</div>
												<div class = "col-12 text-center">
													<div class="progress" style="height: 5px;">
														<div class="progress-bar <?php echo $slusanje_bg; ?>" role="progressbar" style="<?php echo $slusanje_style; ?>" aria-valuenow="<?php echo $slusanje_value; ?>" aria-valuemin="0" aria-valuemax="100"></div>
													</div>
												</div>
											</div>
									<?php
										}else{
									?>
											<div class = "row">
												<div class = "col-12">
													<p class = "text-right" style = "color: #c01d1d ;font-style: normal; font-weight: 500; font-size: 18px;"><?php echo $txtArray["Nisu unešeni podaci o jeziku!"][$languageUser]; ?></p>
												</div>
											</div>
									<?php 
										}


										/*
											ENGLESKI JEZIK START
											*/
												$queryLanguageEnglish = $db->prepare("
													SELECT
														kj_slusanje
													FROM 
														idk_kandidat_jezici 
													WHERE 
														kj_naziv LIKE 'engleski' 
														AND 
														kj_slusanje != ''
														AND 
														kj_kandidatid = :kj_kandidatid 
														AND 
														kj_slusanje NOT LIKE '%Bez znanja%'
													ORDER BY
														kj_slusanje 
													DESC
													LIMIT 1
												");
												$queryLanguageEnglish->execute(array(
													':kj_kandidatid' => $kandidat_id
												));

												if ($queryLanguageEnglish->rowCount() == 1){
													$rowLanguageEnglish = $queryLanguageEnglish->fetch();

													$candidateLanguageEnglish = $rowLanguageEnglish["kj_slusanje"]; 

													if($candidateLanguageEnglish == "A1"){
														$eng_slusanje_bg = "bg-primary";
														$eng_slusanje_style = "width: 17%;";
														$eng_slusanje_value = 17;
													}else if($candidateLanguageEnglish == "A2"){
														$eng_slusanje_bg = "bg-primary";
														$eng_slusanje_style = "width: 34%;";
														$eng_slusanje_value = 34;
													}else if($candidateLanguageEnglish == "B1"){
														$eng_slusanje_bg = "bg-primary";
														$eng_slusanje_style = "width: 50%;";
														$eng_slusanje_value = 50;
													}else if($candidateLanguageEnglish == "B2"){
														$eng_slusanje_bg = "bg-primary";
														$eng_slusanje_style = "width: 66%;";
														$eng_slusanje_value = 66;
													}else if($candidateLanguageEnglish == "C1"){
														$eng_slusanje_bg = "bg-primary";
														$eng_slusanje_style = "width: 83%;";
														$eng_slusanje_value = 83;
													}else if($candidateLanguageEnglish == "C2"){
														$eng_slusanje_bg = "bg-primary";
														$eng_slusanje_style = "width: 100%;";
														$eng_slusanje_value = 100;
													}else{
														$eng_slusanje_bg = "bg-primary";
														$eng_slusanje_style = "width: 1%;";
														$eng_slusanje_value = 1;
													}

													?>

														<div class = "row mb-3">
															<div class = "col-6">
																<p  style = "color: #4B4B4B ;font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Engleski"][$languageUser]; ?></p>
															</div>
															<div class = "col-6 ">
																<p class = "text-right" style = "color: #8E97A3 ;font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;"><?php echo $candidateLanguageEnglish; ?></p>
															</div>
															<div class = "col-12 text-center">
																<div class="progress" style="height: 5px;">
																	<div class="progress-bar <?php echo $eng_slusanje_bg; ?>" role="progressbar" style="<?php echo $eng_slusanje_style; ?>" aria-valuenow="<?php echo $eng_slusanje_value; ?>" aria-valuemin="0" aria-valuemax="100"></div>
																</div>
															</div>
														</div>

													<?php 

												}
											/*
											ENGLESKI JEZIK END
										*/
									?>
								</div>
							</div>
							<?php 
								$vozackaKandidatExp = getVozackaDozvolaKandidat($kandidat_id);
								if(!in_array(100,$vozackaKandidatExp) AND !in_array(102,$vozackaKandidatExp)){
									$vozackaKandidatImp = implode(",", $vozackaKandidatExp);
							?>
							<div class = "row pt-3">
								<div class = "col-12">
									<p style = "color: #B0B4B7 ;font-style: normal; font-weight: normal; font-size: 16px; line-height: 19px;"><?php echo $txtArray["Vozačka dozvola"][$languageUser]; ?></p>
									<div class = "row">
										<div class = "col-12">
											<p  style = "color: #4B4B4B ;font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;"><?php echo $vozackaKandidatImp; ?></p>
										</div>
									</div>
								</div>
							</div>
							<?php 
								}
								unset($vozackaKandidat);
							?>
							<?php 
								if($candidatePocetakRada != NULL AND $flagAccess == 0){
									
							?>
							<div class = "row pt-3 pb-5">
								<div class = "col-12">
									<p style = "color: #B0B4B7 ;font-style: normal; font-weight: normal; font-size: 16px; line-height: 19px;"><?php echo $txtArray["Mogući početak rada"][$languageUser]; ?></p>
									<div class = "row">
										<div class = "col-12">
                                            <p  style = "color: #4B4B4B ;font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;">
                                                <?php 
                                                    echo str_replace("to", "-", $candidatePocetakRada); 
													// $durationPerStatus = new durationPerStatus();
                                                    // $candidatesProjection = new candidatesProjection($durationPerStatus, $kandidat_id);
                                                    // echo date("d.m.Y", $candidatesProjection->getCandidateProjectionRows()[0]["candidate_assessment"]);
                                                ?>
                                            </p>
										</div>
									</div>
								</div>
							</div>
							<?php 
								}
							?>
						</div>
						<?php
						$get_information_info = $db -> prepare("
							SELECT *
							FROM idk_pp_nalog_profil
							INNER JOIN idk_pp_informacije_profil ON idk_pp_nalog_profil.pnp_info_id = idk_pp_informacije_profil.pip_id
							WHERE pnp_nalog_id = :pnp_nalog_id AND pip_status = 1
						");
						
						$get_information_info -> execute(array(
							':pnp_nalog_id' => $candidateNalog
						));

						$hasInfo = $get_information_info ->rowCount();
						
						$row_infos = $get_information_info->fetchAll();
						
						if($hasInfo > 0){ ?>
						<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;margin-top: 16px;">
							<?php foreach($row_infos as $row_info){ 
							?>
							<div class = "row pt-3">
								<div class = "col-12">
									<p style = "color: #B0B4B7 ;font-style: normal; font-weight: normal; font-size: 16px; line-height: 19px;"><?php echo $row_info["pip_name_de"]; ?></p>
									<div class = "row">
										<div class = "col-12">
											<p style = "color: #4B4B4B ;font-style: normal; font-weight: 500; font-size: 18px; line-height: 22px;">
												<?php
													if($row_info["pip_name"] == 'Telefon'){
														echo $candidateTel;
													}elseif($row_info["pip_name"] == 'Email'){
														echo $candidateMail;
													}elseif($row_info["pip_name"] == 'Državljanstvo vrsta'){
														if($kandidat_drzavljanstvo_vrsta != null){
															if($kandidat_drzavljanstvo_vrsta == "NON-EU državljanin"){
																echo "NON-EU";
															}elseif($kandidat_drzavljanstvo_vrsta == "EU državljanin"){
																echo "EU";
															}
														}else{
															echo '-';
														}
													}elseif($row_info["pip_name"] == 'Nacin odlaska'){
														if($kandidat_nacin_odlaska == 0){
															if($kandidat_drzavljanstvo_vrsta == "EU državljanin"){
																echo "EU";
															}else{
																echo "Fachkräfteeinwanderungsgesetz";
															}
														}
														if($kandidat_nacin_odlaska == 1){
															echo "EU";
														}elseif($kandidat_nacin_odlaska == 2){
															echo "Westbalkanregelung";
														}elseif($kandidat_nacin_odlaska == 3){
															echo "Erfahrungssäule";
														}
													}elseif($row_info["pip_name"] == 'Radno iskustvo u struci'){
														if($kandidat_iskustvo_u_struci != null){
															if($kandidat_iskustvo_u_struci == 1){
																echo 'hat Erfahrung';
															}else if($kandidat_iskustvo_u_struci == 0){
																echo 'hat keine Erfahrung';
															}
														}else{
															echo '-';
														}
													}elseif($row_info["pip_name"] == 'Radno iskustvo u struci u posljednjih 5 godina'){
														if($kandidat_iskustvo_u_struci_trajanje != null){
															if($kandidat_iskustvo_u_struci_trajanje == 0){
																echo 'keine Erfahrung';
															}else if($kandidat_iskustvo_u_struci_trajanje == 1){
																echo 'weniger als 1 Jahr';
															}else if($kandidat_iskustvo_u_struci_trajanje == 2){
																echo '1 Jahr';
															}else if($kandidat_iskustvo_u_struci_trajanje == 3){
																echo '2 Jahre';
															}else if($kandidat_iskustvo_u_struci_trajanje == 4){
																echo '3 Jahre';
															}else if($kandidat_iskustvo_u_struci_trajanje == 5){
																echo '4 Jahre';
															}else if($kandidat_iskustvo_u_struci_trajanje == 6){
																echo '5 Jahre';
															}
														}else{
															echo '-';
														}
													}elseif($row_info["pip_name"] == 'Preferirana pokrajina'){
														if($kandidat_zeljena_regija != null){
															if($kandidat_zeljena_regija == 0){
																echo 'keine Präferenz';
															}else{
																echo getRegionNameByIdR($kandidat_zeljena_regija);
															}
														}else{
															echo '-';
														}
													}elseif($row_info["pip_name"] == 'Preferirani grad'){
														if($kandidat_zeljeni_grad != null){
															if($kandidat_zeljeni_grad == 0){
																echo 'keine Präferenz';
															}else{
																echo getCityNameByIdR($kandidat_zeljeni_grad);
															}
														}else{
															echo '-';
														}
													}elseif($row_info["pip_name"] == 'Status nostrifikacije'){
														if($status_dipla_text != NULL){
															echo $status_dipla_text;
														}else{
															echo '-';
														}
													}
												?>
											</p>
										</div>
									</div>
								</div>
							</div>
							<?php 
							} 
							?>
						</div>
						<?php } ?>
					</div>
					<div class="modal fade" id="modalPartner" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modalPartnerLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content border-0">
								<div class="modal-header border-bottom-0 text-center">
									<h5 class="modal-title w-100" id="modalPartnerLabel"><?php echo $txtArray["Detalji ugovora"][$languageUser]; ?></h5>
									<!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
								</div>
								<div class="modal-body px-3">
									<div class = "row pt-1 my-3">
										<div class = "col-12 text-center">
											<div class="mb-3 mx-5 row">
												<div class="col-sm-12">
													<select class="selectpicker form-control" id="partnerIDVal" placeholder = "<?php echo $txtArray["Partner"][$languageUser]; ?>">
														<?php 
															$partneriNaloga = getPartneriNalogaArrayIdR($candidateNalog);
															if(count($partneriNaloga) != 0){
																foreach($partneriNaloga AS $valPartnerId){
																	if(count($partneriNaloga) == 1){
																		$partner_selected = " selected ";
																	}else{
																		$partner_selected = "";
																	}
																	$flag_write 	= true;
																	if($valPartnerId == 11  AND $candidateNalog == 222){
																		$flag_write = false;
																	}
																	if($flag_write){
																		echo '<option value = "'.$valPartnerId.'" '.$partner_selected.'>'.getCompanyNameR(getCompanyForPartnerIdR($valPartnerId)).'</option>';												
																	}
																}
															}
														?>
													</select>
													<script>
														$(document).ready(function() {
															$('#partnerIDVal').selectpicker();
														});
													</script>
												</div>
											</div>
											<div class="mb-3 mx-5 row">
												<div class="col-sm-12">
													<select class="selectpicker form-control" id="partnerLocation" placeholder = "<?php 
														if(hasPartnersR($candidateNalog))
															echo $txtArray["Lokacija Partnera"][$languageUser]; 
														else
															echo $txtArray["Mjesto"][$languageUser];
													?>">
														<?php
														getPartnerLocation($candidateNalog);
														?>
														<option value = "0"><?php echo $txtArray["Ostalo"][$languageUser]; ?></option>
													</select>
													<script>
														$(document).ready(function() {
															$('#partnerLocation').selectpicker();
														});
													</script>
												</div>
											</div>
											<script>
												$(document).on("change","#partnerLocation",function() {
													var partnerLocation = $("#partnerLocation").val();
													
													if(partnerLocation == 0){
														$("#hideShowLocOstalo").show();
													}else{
														$("#hideShowLocOstalo").hide();
													}
												});
											</script>
											<div class="mb-3 mx-5 row" id = "hideShowLocOstalo" style = "display:none;">
												<div class="col-sm-12">
													<input type = "text" class="form-control" id="partnerLocationOstalo" placeholder = "<?php echo $txtArray["Lokacija Partnera"][$languageUser]; ?>">
												</div>
											</div>
											<div class="mb-3 mx-5 row">
												<div class="col-sm-12">
													<select class="selectpicker form-control" id="partnerPosition" placeholder = "<?php echo $txtArray["Radna pozicija"][$languageUser]; ?>">
													<?php
														getWorkingPosition($candidateNalog);
													?>
														<option value = "0"><?php echo $txtArray["Ostalo"][$languageUser]; ?></option>
													<select/>
													<script>
														$(document).ready(function() {
															$('#partnerPosition').selectpicker();
														});
													</script>
												</div>
											</div>
											<script>
												$(document).on("change","#partnerPosition",function() {
													var partnerPosition = $("#partnerPosition").val();
													
													if(partnerPosition == 0){
														$("#hideShowPositionOstalo").show();
													}else{
														$("#hideShowPositionOstalo").hide();
													}
												});
											</script>
											<div class="mb-3 mx-5 row" id = "hideShowPositionOstalo" style = "display:none;">
												<div class="col-sm-12">
													<input type = "text" class="form-control" id="partnerPositionOstalo" placeholder = "<?php echo $txtArray["Radna pozicija"][$languageUser]; ?>">
												</div>
											</div>
											<div class="mb-3 mx-5 row">
												<div class="col-sm-12">
													<input type="text" class="form-control" id="partnerSalary"  placeholder = "<?php echo $txtArray["Iznos plate"][$languageUser]; ?>">
												</div>
											</div>
											<script>
												$('#partnerSalary').on('input', function() {
													this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
												});
											</script>
										</div>
									</div>
								</div>
								<div class="modal-footer justify-content-center border-top-0">
									<button type="button" class="btn btn-secondary kasnijePartnerDugme" data-kan_id_kasnije = "<?php echo $kandidat_key; ?>" data-nalog_id_kasnije = "<?php echo $candidateNalog; ?>"><?php echo $txtArray["Kasnije"][$languageUser]; ?></button>
									<button class="btn btn-primary dodijeliPartneruDugme" data-kan_id_dodijeli = "<?php echo $kandidat_key; ?>" data-nalog_id_dodijeli = "<?php echo $candidateNalog; ?>"><?php echo $txtArray["Dodijeli"][$languageUser]; ?></button>
								</div>
								
							</div>
						</div>
					</div>
					<script>
						$(".dugmePrihvatamKan").on("click", function() {
							var kan_id_prihvatam = $(this).data("kan_id_prihvatam");
							var nalog_id_prihvatam = parseInt($(this).data("nalog_id_prihvatam"));
							
							getAccessControl(nalog_id_prihvatam, kan_id_prihvatam, null, 2, function(response_access_control){
								var reminder_status 			= response_access_control['status'];
								var reminder_id 				= response_access_control['id'];
								var reminder_assigned_user 		= response_access_control['assigned'];
								var reminder_is_logged_user 	= response_access_control['isLogged'];
								var reminder_response_message	= response_access_control['message'];
								var reminder_response_title		= response_access_control['title'];

								if(reminder_status == 101 || reminder_status == 103 || reminder_status == 1 || reminder_status == 2){
									$.ajax({
										url: 'ajax.php?action=hire_candidate',
										type: 'POST',
										dataType: 'html',
										data:{
											'kandidat_id'	: kan_id_prihvatam,
											'nalog_id'		: nalog_id_prihvatam
										},
										success : function (response){
											$("#modalAccept").modal('hide');
											$("#modalPartner").modal('show');
										},
										error: function (xhr, ajaxOptions, thrownError) {
											alert(xhr.status);
											alert(thrownError);
										}
									});
									if(reminder_status == 103 || reminder_status == 1 || reminder_status == 2){ 
										updateReminderStatus(reminder_id, 3, function(response_update_reminder){
											var update_status 	= response_update_reminder["status"];
											var update_message 	= response_update_reminder["message"];
											if(update_status == 3){
												showToast(update_message, reminder_response_title);
											}
										});
									}
								}
								else if(reminder_status == 104){
									showToast(reminder_response_message, reminder_response_title);
								}
								else{
									showToast(reminder_response_message, reminder_response_title);
								}
							});
						});
						$(".dugmeOdbijamKan").on("click",function() {
							var kan_id_odbijam = $(this).data("kan_id_odbijam");
							var nalog_id_odbijam = parseInt($(this).data("nalog_id_odbijam"));
							var nalog_termin_id = parseInt($(this).data("nalog_termin_id"));
							var razlog_odbijanja = $('#razlog_odbijanja').val();
							var razlog_odbijanja_id = $('#razlogOdbijanjaId').val();
							var type = parseInt("<?php echo 1; ?>");
							var bar_id = parseInt("<?php echo 5; ?>");
							
							getAccessControl(nalog_id_odbijam, kan_id_odbijam, null, 2, function(response_access_control){

								var reminder_status 			= response_access_control['status'];
								var reminder_id 				= response_access_control['id'];
								var reminder_assigned_user 		= response_access_control['assigned'];
								var reminder_is_logged_user 	= response_access_control['isLogged'];
								var reminder_response_message	= response_access_control['message'];
								var reminder_response_title		= response_access_control['title'];

								if (reminder_status == 101 || reminder_status == 103 || reminder_status == 1 || reminder_status == 2) {
									if(razlog_odbijanja_id == "" ){
										$('#razlogOdbijanjaIdEffect').effect('shake');
										$('#razlogOdbijanjaIdEffect').effect('bounce');
										// if(razlog_odbijanja_id == ""){
											// $('#razlogOdbijanjaIdEffect').effect('shake');
											// $('#razlogOdbijanjaIdEffect').effect('bounce');
										// }
										// if(razlog_odbijanja == ""){
											// $('#razlog_odbijanja').effect('shake');
											// $('#razlog_odbijanja').effect('bounce');
										// }
									}else{
										if(reminder_status == 103 || reminder_status == 1 || reminder_status == 2){
											updateReminderStatus(reminder_id, 3, function(response_update_reminder){
												var update_status 	= response_update_reminder["status"];
												var update_message 	= response_update_reminder["message"];
												if(update_status == 3){
													showToast(update_message, reminder_response_title);
												}

											});
										}
										$.ajax({
											url: 'ajax.php?action=reject_candidate',
											type: 'POST',
											dataType: 'html',
											data:{
												'kandidat_id'		: kan_id_odbijam,
												'nalog_id'			: nalog_id_odbijam,
												'razlog_odbijanja_id'	: razlog_odbijanja_id,
												'nalog_termin_id'	: nalog_termin_id
											},
											success : function (response){
												$("#modalOdbij").modal('hide');
												location.href = '<?php getSiteUrl(); ?>'+'profile?bar_id='+bar_id+'&type='+type+'&kandidat_id='+kan_id_odbijam+'&n='+nalog_id_odbijam;

											},
											error: function (xhr, ajaxOptions, thrownError) {
												alert(xhr.status);
												alert(thrownError);
											}
										});
									}
								}
								else if(reminder_status == 104){
									showToast(reminder_response_message, reminder_response_title);
								}
								else{
									showToast(reminder_response_message, reminder_response_title);
								}
							});
							
						});
						$(".kasnijePartnerDugme").on("click",function() {
							var kan_id_kasnije = $(this).data("kan_id_kasnije");
							var nalog_id_kasnije = parseInt($(this).data("nalog_id_kasnije"));
							var type = parseInt("<?php echo 1; ?>");
							var bar_id = parseInt("<?php echo 4; ?>");
							location.href = '<?php getSiteUrl(); ?>'+'profile?bar_id='+bar_id+'&type='+type+'&kandidat_id='+kan_id_kasnije+'&n='+nalog_id_kasnije;

						});
						$(".dodijeliPartneruDugme").on("click",function() {
							var kan_id_dodijeli = $(this).data("kan_id_dodijeli");
							var nalog_id_dodijeli = parseInt($(this).data("nalog_id_dodijeli"));
							
							var partnerIDVal = parseInt($("#partnerIDVal").val());
							var partnerLocation = $("#partnerLocation").val();
							var partnerLocationOstalo = $("#partnerLocationOstalo").val();
							var partnerPosition = $("#partnerPosition").val();
							var partnerPositionOstalo = $("#partnerPositionOstalo").val();
							var partnerSalary = $("#partnerSalary").val();
							
							var type = parseInt("<?php echo 2; ?>");
							var bar_id = parseInt("<?php echo 2; ?>");

							getAccessControl(nalog_id_dodijeli, kan_id_dodijeli, null, 3, function(response_access_control){

								var reminder_status 			= response_access_control['status'];
								var reminder_id 				= response_access_control['id'];
								var reminder_assigned_user 		= response_access_control['assigned'];
								var reminder_is_logged_user 	= response_access_control['isLogged'];
								var reminder_response_message	= response_access_control['message'];
								var reminder_response_title		= response_access_control['title'];

								if (reminder_status == 101 || reminder_status == 103 || reminder_status == 1 || reminder_status == 2) {
									$(this).prop('disabled', true);
									
									if(partnerIDVal == ''){
										$(this).prop('disabled', false);
										$('#partnerIDVal').parent().effect('bounce');
									}else{
										$.ajax({
											url: 'ajax.php?action=assign_candidate_to_partner',
											type: 'POST',
											dataType: 'html',
											data:{
												'partnerIDVal'			: partnerIDVal,
												'partnerLocation'		: partnerLocation,
												'partnerLocationOstalo'	: partnerLocationOstalo,
												'partnerPosition'		: partnerPosition,
												'partnerPositionOstalo'	: partnerPositionOstalo,
												'partnerSalary'			: partnerSalary,
												'candidate_id'			: kan_id_dodijeli,
												'nalog_id'				: nalog_id_dodijeli
											},
											success : function (response){
												$("#modalPartner").modal('hide');
												location.href = '<?php getSiteUrl(); ?>'+'profile?bar_id='+bar_id+'&type='+type+'&kandidat_id='+kan_id_dodijeli+'&n='+nalog_id_dodijeli;
											},
											error: function (xhr, ajaxOptions, thrownError) {
												alert(xhr.status);
												alert(thrownError);
											}
										});

										if(reminder_status == 103 || reminder_status == 1 || reminder_status == 2){
											updateReminderStatus(reminder_id, 3, function(response_update_reminder){
												var update_status 	= response_update_reminder["status"];
												var update_message 	= response_update_reminder["message"];
												if(update_status == 3){
													showToast(update_message, reminder_response_title);
												}
											});
										}	
									}
								}
								else if(reminder_status == 104){
									showToast(reminder_response_message, reminder_response_title);
								}
								else{
									showToast(reminder_response_message, reminder_response_title);
								}
							});	
							
							
							// var prazniInputFlag = 0;
							// if(partnerIDVal === 0){
								// $("#partnerIDVal").effect('shake' ,500);
								// prazniInputFlag = 1;
							// }
							// if(partnerLocation == ""){
								// $("#partnerLocation").effect('shake' ,500);
								// prazniInputFlag = 1;
							// }
							// if(partnerLocation == "Ostalo" && partnerLocationOstalo == ""){
								// $("#partnerLocationOstalo").effect('shake' ,500);
								// prazniInputFlag = 1;
							// }
							// if(partnerPosition == ""){
								// $("#partnerPosition").effect('shake' ,500);
								// prazniInputFlag = 1;
							// }
							// if(partnerPosition == "Ostalo" && partnerPositionOstalo == ""){
								// $("#partnerPositionOstalo").effect('shake' ,500);
								// prazniInputFlag = 1;
							// }
							// if(partnerSalary == ""){
								// $("#partnerSalary").effect('shake' ,500);
								// prazniInputFlag = 1;
							// }
							
							// if(prazniInputFlag == 0){
								// //pozovi funkciju i odradi sve
							// }
							
							// if(partnerPosition != "Ostalo"){
								// partnerPosition = partnerPosition;
							// }else{
								// partnerPosition = partnerPositionOstalo;
							// }
							
							// if(partnerLocation != "Ostalo"){
								// partnerLocation = partnerLocation;
							// }else{
								// partnerLocation = partnerLocationOstalo;
							// }
							
							// console.log(kan_id_dodijeli + " " + nalog_id_dodijeli+ " " + partnerIDVal+ " " + partnerLocation+ " " + partnerPosition+ " " + partnerSalary);
							//Ovdje ide poziv funkcije koja ce azurirati sve u pozadini

						});
					</script>
					<div class = "col-md-8 mb-5">
						<div class = "row pt-3">
						<?php 
							if($type == 2 AND ($kandidat_status_prijave == 7))
							{
								//Priprema za dio Detalji o ugovoru START
								$contractDetailsFlag = false;
								$contractDetailsButton = '';
								if(!is_null($candidatePartnerId)){
									if($kandidat_status_prijave == 7){
										$contractDetailsButton = '
											<i 
												class="fa fa-pencil fa-4x click_edit_candidate" 
												id = "child_edit_candidate'.$kandidat_key.'" 
												nalog_id = "'.$candidateNalog.'" 
												candidate_id = "'.$kandidat_key.'" 
												partner_id = "'.$candidatePartnerId.'" 
												candidate_location = "'.$kandidat_pp_lokacija.'" 
												candidate_position = "'.$kandidat_pp_pozicija.'" 
												candidate_salary = "'.$kandidat_pp_plata.'" 
												aria-hidden="true" 
												onclick="handleEditCandidatePartnerDataModalOpen(this)"
											>
											</i>
											<br>
											<b>
												'.$txtArray["Detalji ugovora"][$languageUser].'
											</b>
										';
										$contractDetailsFlag = true; 
									}else{
										$contractDetailsFlag = false; 
										$contractDetailsButton = '';
									}
								}else{
									$contractDetailsFlag = true; 
									$contractDetailsButton = '
										<i 
											class="fa fa-times fa-building-o fa-4x click_hire_candidate" 
											id = "child_assign_partner'.$kandidat_key.'" 
											nalog_id = "'.$candidateNalog.'" 
											candidate_id = "'.$kandidat_key.'" 
											aria-hidden="true" 
											onclick="handleAsignCandidateModalOpen(this)" 
											style = "font-size: 4em!important;"
										>
										</i>
										<br>
										<b>
											'.$txtArray["Detalji ugovora"][$languageUser].'
										</b>
									';
								}
								//Priprema za dio Detalji o ugovoru END 

								//Priprema za dio Slanje ugovora START
								$contractSentFlag = false;
								$contractSentButton = '';
								if($kandidat_status_prijave == 7){
									$contractSentFlag = true; 
									$contractSentButton = '
										<i 
											class="fa fa-files-o fa-4x click_manage_documents" 
											id = "click_manage_documents'.$kandidat_key.'" 
											nalog_id = "'.$candidateNalog.'" 
											candidate_id = "'.$kandidat_key.'" 
											partner_id = "'.$candidatePartnerId.'" 
											location = "p" 
											aria-hidden="true" 
											onclick="handleManageContractSentModalOpen(this)"
										>
										</i>
										<br>
										<b>
											'.$txtArray["Označavanje slanja ugovora"][$languageUser].'
										</b>
									';
								}else{
									$contractSentFlag = false;
									$contractSentButton = '';
								}
								//Priprema za dio Slanje ugovora START

								//Na osnovu pripreme - postavljanje klasa za izgled - odnosno grid sistem START
								$documentsGridClass = ""; 
								if($contractDetailsFlag == true AND $contractSentFlag == true){
									$documentsGridClass = "col-lg-6";
								}else{
									$documentsGridClass = "col-lg-12";
								}
								//Na osnovu pripreme - postavljanje klasa za izgled - odnosno grid sistem END 
								
						?>
							<div class = "col-12">
								<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
									<div class = "row">
										<div class = "col-12">
											<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Dokumenti"][$languageUser]; ?></p>
										</div>
									</div>
									<div class = "row pt-3">
										<div class = "col-12 text-center" style="overflow-y: auto !important;">
											<div class = "row">
												<?php 
													//Slanje ugovora START
													if($contractSentFlag == true){
												?>
													<div class = "<?php echo $documentsGridClass; ?>">
														<?php echo $contractSentButton; ?>
													</div>
												<?php 
													}
													//Slanje ugovora END 

													//Detalji ugovora START
													if($contractDetailsFlag == true){
												?>
													<div class = "<?php echo $documentsGridClass; ?>">
														<?php echo $contractDetailsButton; ?>
													</div>
												<?php 
													}
													//Detalji ugovora END 
												?>
											</div>
											<div class="modal fade" id="modalEditCandidatePartnerData" data-bs-keyboard="false" aria-labelledby="modalEditCandidatePartnerData" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
												<div class="modal-dialog modal-dialog-centered">
													<div class="modal-content border-0">
														<div class="modal-header border-bottom-0 text-center">
															<h5 class="modal-title w-100" id="modalPartnerLabel"><?php echo $txtArray["Ažuriraj podatke"][$languageUser]; ?></h5>
															<!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
														</div>
														<div class="modal-body px-3">
															<div class = "row pt-1 my-3">
																<div class = "col-12 text-center">
																	<div class="mb-3 mx-5 row">
																		<div class="col-sm-12">
																			<label style = "width:100%;text-align:left;"><?php echo $txtArray["Partner"][$languageUser]; ?>:</label>
																			<select class="selectpicker form-control" id="editPartnerIDVal">
																				<?php 
																					$partneriNaloga = getPartneriNalogaArrayIdR($candidateNalog);
																					if(count($partneriNaloga) != 0){
																						foreach($partneriNaloga AS $valPartnerId){
																							$flag_write 	= true;
																							if($valPartnerId == 11  AND $candidateNalog == 222){
																								$flag_write = false;
																							}
																							if($flag_write){
																								echo '<option value = "'.$valPartnerId.'">'.getCompanyNameR(getCompanyForPartnerIdR($valPartnerId)).'</option>';												
																							}
																						}
																					}
																				?>
																			</select>
																			<script>
																				$(document).ready(function() {
																					$('#editPartnerIDVal').selectpicker();
																				});
																			</script>
																		</div>
																	</div>
																	<div class="mb-3 mx-5 row">
																		<div class="col-sm-12">
																			<label style = "width:100%;text-align:left;">
																			<?php 
																				if(hasPartnersR($candidateNalog))
																					echo $txtArray["Lokacija Partnera"][$languageUser]; 
																				else
																					echo $txtArray["Mjesto"][$languageUser];
																			?>
																			</label>
																			<select class="selectpicker form-control" id="editPartnerLocation">
																				<?php
																				getPartnerLocation($candidateNalog);
																				?>
																				<option value = "0"><?php echo $txtArray["Ostalo"][$languageUser]; ?></option>
																			</select>
																			<script>
																				$(document).ready(function() {
																					$('#editPartnerLocation').selectpicker();
																				});
																			</script>
																		</div>
																	</div>
																	<script>
																		$(document).on("change","#editPartnerLocation",function() {
																			var partnerLocation = $("#editPartnerLocation").val();
																			
																			if(partnerLocation == 0){
																				$("#editHideShowLocOstalo").show();
																			}else{
																				$("#editHideShowLocOstalo").hide();
																			}
																		});
																	</script>
																	<div class="mb-3 mx-5 row" id = "editHideShowLocOstalo" style = "display:none;">
																		<div class="col-sm-12">
																			<input type = "text" class="form-control" id="editPartnerLocationOstalo" placeholder = "<?php echo $txtArray["Lokacija Partnera"][$languageUser]; ?>">
																		</div>
																	</div>
																	<div class="mb-3 mx-5 row">
																		<div class="col-sm-12">
																			<label style = "width:100%;text-align:left;"><?php echo $txtArray["Radna pozicija"][$languageUser]; ?></label>
																			<select class="selectpicker form-control" id="editPartnerPosition" >
																			<?php
																				getWorkingPosition($candidateNalog);
																			?>
																				<option value = "0"><?php echo $txtArray["Ostalo"][$languageUser]; ?></option>
																			<select/>
																			<script>
																				$(document).ready(function() {
																					$('#editPartnerPosition').selectpicker();
																				});
																			</script>
																		</div>
																	</div>
																	<script>
																		$(document).on("change","#editPartnerPosition",function() {
																			var partnerPosition = $("#editPartnerPosition").val();
																			
																			if(partnerPosition == 0){
																				$("#editHideShowPositionOstalo").show();
																			}else{
																				$("#editHideShowPositionOstalo").hide();
																			}
																		});
																	</script>
																	<div class="mb-3 mx-5 row" id = "editHideShowPositionOstalo" style = "display:none;">
																		<div class="col-sm-12">
																			<input type = "text" class="form-control" id="editPartnerPositionOstalo" placeholder = "<?php echo $txtArray["Radna pozicija"][$languageUser]; ?>">
																		</div>
																	</div>
																	<div class="mb-3 mx-5 row">
																		<div class="col-sm-12">
																			<label style = "width:100%;text-align:left;"><?php echo $txtArray["Iznos plate"][$languageUser]; ?></label>
																			<input type="text" class="form-control" id="editPartnerSalary">
																		</div>
																	</div>
																	<script>
																		$('#editPartnerSalary').on('input', function() {
																			this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
																		});
																	</script>
																</div>
															</div>
														</div>
														<div class="modal-footer justify-content-center border-top-0">
															<button class="btn btn-primary" id = "edit_candidate_partner_data" location = "p"><?php echo $txtArray["Ažuriraj"][$languageUser]; ?></button>
														</div>
														
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php
							}
						?>
						</div>
						<style>
							.dataTables_wrapper{
								margin-bottom: 15px;
							}
						</style>
						<!-- 
							********************************************************************************************************************************************************************
							****															HISTORIJA DOKUMENATA START																		****
							********************************************************************************************************************************************************************
						-->
						<?php 

							if((in_array($kandidat_status_prijave, array(4,10,15,18,21,24,27))) OR ($bar_id == 5 AND $type == 1)){

								include("includes/documents/documentHistory.php");

							}
						?>
						<!-- 
							********************************************************************************************************************************************************************
							****															HISTORIJA DOKUMENATA END 																		****
							********************************************************************************************************************************************************************
						-->
						<!-- 
							********************************************************************************************************************************************************************
							****															DOKUMENTI PROCESA ODBIJENICE/DOPUNE START														****
							********************************************************************************************************************************************************************
						-->
						<!-- Ovdje je potrebno napraviti uslov koji ce odrediti u kojim segmentima procesa će biti dole naslovljeni dio vidljiv -->
						<?php 
							$flagActiveVisaIncompleteDopuna 		= getActiveVisaIncompleteR($kandidat_id, 1, $candidateNalog);
							$flagActiveVisaIncompleteOdbijenica 	= getActiveVisaIncompleteR($kandidat_id, 2, $candidateNalog);
							if($flagActiveVisaIncompleteDopuna != 0 OR $flagActiveVisaIncompleteOdbijenica != 0){
								$visaIncompleteId = 0;
								$visaIncompleteTitle = "";
								if($flagActiveVisaIncompleteDopuna != 0 AND $flagActiveVisaIncompleteOdbijenica == 0){
									$visaIncompleteId = $flagActiveVisaIncompleteDopuna;
									$visaIncompleteTitle = $txtArray["Dopuna"][$languageUser];
								}else if($flagActiveVisaIncompleteDopuna == 0 AND $flagActiveVisaIncompleteOdbijenica != 0){
									$visaIncompleteId = $flagActiveVisaIncompleteOdbijenica;
									$visaIncompleteTitle = $txtArray["Odbijenica"][$languageUser];
								}else{
									$visaIncompleteId = 0;
									$visaIncompleteTitle = "";
								}

								if($visaIncompleteId != 0){
									$detailsVisaIncomplete = getDetailsForVisaIncompleteArrayR($visaIncompleteId);
									$countDocVisaIncomplete = getCountDocumentsVisaIncomplete($kandidat_id, $candidateNalog, $visaIncompleteId);
						?>
						<div class="row pt-3">
							<div class="col-12">
								<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
									<div class = "row">
										<div class = "col-12">
											<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $visaIncompleteTitle; ?></p>
										</div>
									</div>
									<?php 
										if($countDocVisaIncomplete != 0){
									?>
									<script>
										function getReadyToSendVI(nalogId, candidateId, viId){
											$.ajax({
												url: 'ajax.php?action=readyToSendVisaIncomplete',
												type: 'POST',
												dataType: 'html',
												data:{
													'idNalog'			: nalogId,
													'idCandidate'		: candidateId,
													'viId'				: viId
												},
												success : function (response){
													if(response != ""){
														$(".readyToSendButtonVI").html(response);
														$('.readyToSendVI').removeClass('visually-hidden');
														/*$(".readyToSendButton").html(response, function(){
															$('.readyToSend').removeClass('visually-hidden', function(){
																alert("Adis");
															});
														});*/
													}else{
														if($(".readyToSendVI").hasClass("visually-hidden") == false){
															$('.readyToSendVI').addClass('visually-hidden');
														}
													}
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
										};
									</script>
									<div class="row pt-3 visually-hidden readyToSendVI">
										<div class="col-12 text-center readyToSendButtonVI">
											
										</div>
									</div>
									<?php 
										}
									?>
									<div class = "row pt-1">
										<div class = "col-12 scrollBarHorizontal" style="overflow-y: auto !important; user-select: none;">
											<!-- 
												DETALJI DOPUNE/ODBIJENICE START
											-->
											<div class="row">
												<div class="col-lg-12">
													<?php 
														$flagDocuments = 0;
														if($detailsVisaIncomplete["vi_type"][0] == 1 OR ($detailsVisaIncomplete["vi_type"][0] == 2 AND $detailsVisaIncomplete["vi_complaint"][0] == 1)){
															$flagDocuments = 1;
													?> 
													<table class="table table-borderless">
														<thead>
															<tr>
																<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Zaprimio"][$languageUser];?></p></th>
																<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Datum prijema"][$languageUser];?></p></th>
																<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Krajnji datum"][$languageUser];?></p></th>
															</tr>
														</thead>
														<tbody>
															<?php 
																if($detailsVisaIncomplete["vi_date_received_candidate"][0] != NULL AND $detailsVisaIncomplete["vi_deadline_date_candidate"][0] != NULL){
															?>
															<tr>
																<td class="text-center pt-0"><span class="badge bg-secondary"><?php echo $txtArray["Kandidat"][$languageUser];?></span></td>
																<td class="text-center pt-0"><span class="badge bg-light text-dark"><?php echo date("d.m.Y H:i", strtotime($detailsVisaIncomplete["vi_date_received_candidate"][0]));?></span></td>
																<td class="text-center pt-0"><span class="badge bg-light text-dark"><?php echo date("d.m.Y", strtotime($detailsVisaIncomplete["vi_deadline_date_candidate"][0]));?></span></td>
															</tr>
															<?php 
																}
																if($detailsVisaIncomplete["vi_date_received_employer"][0] != NULL AND $detailsVisaIncomplete["vi_deadline_date_employer"][0] != NULL){
															?>
															<tr>
																<td class="text-center pt-0"><span class="badge bg-secondary"><?php echo $txtArray["Poslodavac"][$languageUser];?></span></td>
																<td class="text-center pt-0"><span class="badge bg-light text-dark"><?php echo date("d.m.Y H:i", strtotime($detailsVisaIncomplete["vi_date_received_employer"][0]));?></span></td>
																<td class="text-center pt-0"><span class="badge bg-light text-dark"><?php echo date("d.m.Y", strtotime($detailsVisaIncomplete["vi_deadline_date_employer"][0]));?></span></td>
															</tr>
															<?php 
																}
															?>
														</tbody>
													</table>
													<?php 
														}else{
															$flagDocuments = 0;
													?>
													<table class="table table-borderless">
														<thead>
															<tr>
																<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Zaprimio"][$languageUser];?></p></th>
																<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Datum prijema"][$languageUser];?></p></th>
															</tr>
														</thead>
														<tbody>
															<?php 
																if($detailsVisaIncomplete["vi_date_received_candidate"][0] != NULL){
															?>
															<tr>
																<td class="text-center pt-0"><span class="badge bg-secondary"><?php echo $txtArray["Kandidat"][$languageUser];?></span></td>
																<td class="text-center pt-0"><span class="badge bg-light text-dark"><?php echo date("d.m.Y H:i", strtotime($detailsVisaIncomplete["vi_date_received_candidate"][0]));?></span></td>
															</tr>
															<?php 
																}
																if($detailsVisaIncomplete["vi_date_received_employer"][0] != NULL){
															?>
															<tr>
																<td class="text-center pt-0"><span class="badge bg-secondary"><?php echo $txtArray["Poslodavac"][$languageUser];?></span></td>
																<td class="text-center pt-0"><span class="badge bg-light text-dark"><?php echo date("d.m.Y H:i", strtotime($detailsVisaIncomplete["vi_date_received_employer"][0]));?></span></td>
															</tr>
															<?php 
																}
															?>
														</tbody>
													</table>
													<?php 
														}
													?>
												</div>
											</div>
											<!-- 
												DETALJI DOPUNE/ODBIJENICE END
											-->
											<!-- 
												DOKUMENTI DOPUNE/ODBIJENICE START
											-->
											<?php 
												if($flagDocuments == 1){
											?>
											<hr class="mb-0 mt-0">
												<?php 
													if($countDocVisaIncomplete != 0){
												?>
													<script>
														function destroyTableVI(){
															if ($.fn.DataTable.isDataTable('#visaIncompleteDocuments')) {
																$("#visaIncompleteDocuments").DataTable().clear().destroy();
																$("#visaIncompleteDocuments").empty();
															}
														};
														function enablePopoversAndTooltipsVI(){
															var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
															var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
																return new bootstrap.Popover(popoverTriggerEl)
															})
															var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
															var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
																return new bootstrap.Tooltip(tooltipTriggerEl)
															})
														};
														function getRequiredDocumentsVI(nalogId, candidateId, viId){
															destroyTableVI();
															$.ajax({
																url: 'ajax.php?action=visaIncompleteDocuments',
																type: 'POST',
																dataType: 'html',
																data:{
																	'idNalog'			: nalogId,
																	'idCandidate'		: candidateId,
																	'viId'				: viId
																},
																success : function (response){
																	if(response != ""){
																		//$("#requiredDocuments tbody").html("");
																		$("#visaIncompleteDocuments").html(response);
																		if($.fn.DataTable.isDataTable('#visaIncompleteDocuments') == false){
																			$('#visaIncompleteDocuments').DataTable({
																				responsive: false,
																				"order": [[ 0, "desc" ]],
																				"bAutoWidth": false,
																				"bPaginate" : false,
																				"bLengthChange": false,
																				"bInfo": false,
																				"bFilter": false,
																				"aoColumns": [
																					{ "width": "30%" },
																					{ "width": "7.5%", "bSortable": false },
																					{ "width": "22.5%", "bSortable": false },
																					{ "width": "10%", "bSortable": false },
																					{ "width": "10%", "bSortable": false },
																					{ "width": "10%", "bSortable": false },
																					{ "width": "10%", "bSortable": false }
																				]
																			});
																			enablePopoversAndTooltipsVI();
																			getReadyToSendVI(nalogId, candidateId, viId);
																		}
																	}else{
																		if($.fn.DataTable.isDataTable('#visaIncompleteDocuments') == false){
																			$('#visaIncompleteDocuments').DataTable({
																				responsive: false,
																				"order": [[ 0, "desc" ]],
																				"bAutoWidth": false,
																				"bPaginate" : false,
																				"bLengthChange": false,
																				"bInfo": false,
																				"bFilter": false,
																				"aoColumns": [
																					{ "width": "30%"},
																					{ "width": "7.5%", "bSortable": false },
																					{ "width": "22.5%", "bSortable": false },
																					{ "width": "10%", "bSortable": false },
																					{ "width": "10%", "bSortable": false },
																					{ "width": "10%", "bSortable": false },
																					{ "width": "10%", "bSortable": false }
																				]
																			});
																		}
																	}
																},
																error: function (xhr, ajaxOptions, thrownError) {
																	alert(xhr.status);
																	alert(thrownError);
																}
															});
														};
														$(document).ready(function() {
															var idCandidate = parseInt("<?php echo $kandidat_id; ?>");
															var idNalog = parseInt("<?php echo $candidateNalog; ?>");
															var viId = parseInt("<?php echo $visaIncompleteId; ?>");
															getRequiredDocumentsVI(idNalog, idCandidate, viId);
															
														});
													</script>
													<div class="row">
														<div class="col-lg-12">
															<table id="visaIncompleteDocuments" class="display" cellspacing="0" width = "100%">

															</table>
															
															<?php 
																include("includes/documents/modalVisaIncomplete.php");
															?>
														</div>
													</div>
												<?php 
													}else{
												?>
													<div class="row mt-3">
														<div class="col-lg-12">
															<div class="alert alert-warning text-center" role="alert">
																<?php echo $txtArray["Za ovu dopunu/odbijenicu niste zaduženi ni za jedan dokument"][$languageUser]."!"; ?>
															</div>
														</div>
													</div>
												<?php 
													}
												?>
											<!-- 
												DOKUMENTI DOPUNE/ODBIJENICE END
											-->
											<?php 
												}else{
                                            ?>
                                            <hr class="mb-3 mt-0">
											<div class="row">
												<div class="col-lg-12">
                                                    <div class="alert alert-warning text-center" role="alert">
                                                        <?php echo $txtArray["Kod odbijenice nema uslova za žalbu!"][$languageUser]; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php   
                                                }
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php 
									unset($detailsVisaIncomplete);
								}
							}
						?>
						<!-- Ovdje je potrebno napraviti uslov koji ce odrediti u kojim segmentima procesa će biti dole naslovljeni dio vidljiv -->
						<!-- 
							********************************************************************************************************************************************************************
							****															DOKUMENTI PROCESA ODBIJENICE/DOPUNE END															****
							********************************************************************************************************************************************************************
						-->
						
						<!-- 
							********************************************************************************************************************************************************************
							****															NOSTRIFICIRANA DIPLOMA START																	****
							********************************************************************************************************************************************************************
						-->
						<?php 
							if($kandidat_status_prijave == 9 OR $kandidat_status_prijave == 12 OR $kandidat_status_prijave == 3){
								$nostrificationArray = getNostrificationDocument($kandidat_id);
								if(count($nostrificationArray["count"]) == 1){
						?>
							<div class="row pt-3">
								<div class="col-12">
									<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
										<div class = "row">
											<div class = "col-12">
												<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Nostrifikacija"][$languageUser]; ?></p>
											</div>
										</div>
										<div class = "row pt-3">
											<div class = "col-12 scrollBarHorizontal" style="overflow-y: auto !important; user-select: none;">
												<table class="table table-borderless mb-0">
													<thead>
														<tr>
															<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Korisnik"][$languageUser];?></p></th>
															<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Datum prijema"][$languageUser];?></p></th>
															<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Pregled dokumenata"][$languageUser];?></p></th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td class="text-center"><span class="badge bg-light text-dark fs-5"><?php echo $nostrificationArray["employee"][0]; ?></span></td>
															<td class="text-center"><span class="badge bg-light text-dark fs-5"><?php echo $nostrificationArray["date"][0]; ?></span></td>
															<td class="text-center"><button class="badge bg-light text-dark preuzmi_diplomu"  style="border: none; padding: 0;"><a href="<?php echo getCRMUrlr().$nostrificationArray["file"][0];?>" target="_blank" style="text-decoration: none;" class="badge bg-light text-dark fs-5 downloadNostrificationLink"> <img src="images/icons/Upload.svg" width="24px" height="20px" alt=""> </a></button> </td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
									<?php 
									$get_partner_id_query = $db ->prepare("
												SELECT kandidat_ppa_partner_id FROM idk_kandidati WHERE kandidat_id=:kandidat_id
									");
									$get_partner_id_query->execute(array(
										":kandidat_id"=>$kandidat_id
									));
									$get_partner_id = $get_partner_id_query->fetch();
									$partner_id = $get_partner_id['kandidat_ppa_partner_id'];
						
									if(isset($kandidat_key) AND isset($partner_id) AND isset($nalog_id)){
										?>
										<script>
											$(".preuzmi_diplomu").on("click", function () {
												//Kod za uzimanje traženih parametara u ostatku funkcije
												
												var candidateIdKey = "<?php echo $kandidat_key; ?>"; 
												var nalogId = parseInt("<?php echo $nalog_id; ?>");
												var partnerId = <?php echo $partner_id; ?>; 
												var reminderType = 8;
												
												getAccessControl(nalogId, candidateIdKey, partnerId, reminderType, function (responseAccessControl) {
													var reminderStatus = responseAccessControl['status'];
													var reminderId = responseAccessControl['id'];
													var reminderAssignedUser = responseAccessControl['assigned'];
													var reminderIsLoggedUser = responseAccessControl['isLogged'];
													var reminderResponseMessage = responseAccessControl['message'];
													var reminderResponseTitle = responseAccessControl['title'];
													if (reminderStatus == 101 || reminderStatus == 103 || reminderStatus == 1 || reminderStatus == 2) {
														if(reminderStatus == 103 || reminderStatus == 1 || reminderStatus == 2){
															updateReminderStatus(reminderId, 3, function (responseUpdateReminder){
																var updateStatus = responseUpdateReminder["status"];
																var updateMessage = responseUpdateReminder["message"];
																if(updateStatus == 3){
																	showToast(updateMessage, reminderResponseTitle);
																}
															});
														}
													}else if(reminderStatus == 104){
														showToast(reminderResponseMessage, reminderResponseTitle);
													}else{
														showToast(reminderResponseMessage, reminderResponseTitle);
													}
												});
											});

										</script>
									<?php 
									}
								}
							}
						?> 
						<!-- 
							********************************************************************************************************************************************************************
							****															NOSTRIFICIRANA DIPLOMA END   																	****
							********************************************************************************************************************************************************************
						-->

						<!-- 
							********************************************************************************************************************************************************************
							****															DOKUMENTI PROCESA ODLASKA START																	****
							********************************************************************************************************************************************************************
						-->
						<!-- Ovdje je potrebno napraviti uslov koji ce odrediti u kojim segmentima procesa će biti dole naslovljeni dio vidljiv -->
						<?php 
							if($kandidat_status_prijave == 12){
								$countDocuments = getCountDocuments($candidateNalog, $kandidat_id);
						?>
						
						<div class="row pt-3">
							<div class="col-12">
								<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
									<div class = "row">
										<div class = "col-12">
											<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Potrebna dokumentacija"][$languageUser]; ?></p>
										</div>
									</div>
									<?php 
										if($countDocuments != 0){
									?>
									<!-- 
										*************************************************************
										*			Mogucnost slanja dokumenata START				*
										*************************************************************
									-->
									<script>
										function getReadyToSend(nalogId, candidateId){
											$.ajax({
												url: 'ajax.php?action=readyToSend',
												type: 'POST',
												dataType: 'html',
												data:{
													'idNalog'			: nalogId,
													'idCandidate'		: candidateId
												},
												success : function (response){
													if(response != ""){
														$(".readyToSendButton").html(response);
														$('.readyToSend').removeClass('visually-hidden');
														/*$(".readyToSendButton").html(response, function(){
															$('.readyToSend').removeClass('visually-hidden', function(){
																alert("Adis");
															});
														});*/
													}else{
														if($(".readyToSend").hasClass("visually-hidden") == false){
															$('.readyToSend').addClass('visually-hidden');
														}
													}
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
										};
									</script>
									<div class="row pt-3 visually-hidden readyToSend">
										<div class="col-12 text-center readyToSendButton">
											
										</div>
									</div>
									<!-- 
										*************************************************************
										*			Mogucnost slanja dokumenata END 				*
										*************************************************************
									-->
									<?php 
										}

										if($countDocuments != 0){
									?>
									<!-- 
										*************************************************************
										*				DOKUMENTI PROCESA ODLASKA START				*
										*************************************************************
									-->
									<script>
										function destroyTable(){
											if ($.fn.DataTable.isDataTable('#requiredDocuments')) {
												$("#requiredDocuments").DataTable().clear().destroy();
												$("#requiredDocuments").empty();
											}
										};
										function enablePopoversAndTooltips(){
											var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
											var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
												return new bootstrap.Popover(popoverTriggerEl)
											})
											var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
											var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
												return new bootstrap.Tooltip(tooltipTriggerEl)
											})
										};
										function getRequiredDocuments(nalogId, candidateId){
											destroyTable();
											$.ajax({
												url: 'ajax.php?action=requiredDocuments',
												type: 'POST',
												dataType: 'html',
												data:{
													'idNalog'			: nalogId,
													'idCandidate'		: candidateId
												},
												success : function (response){
													if(response != ""){
														//$("#requiredDocuments tbody").html("");
														$("#requiredDocuments").html(response);
														if($.fn.DataTable.isDataTable('#requiredDocuments') == false){
															$('#requiredDocuments').DataTable({
																responsive: false,
																"order": [[ 0, "desc" ]],
																"bAutoWidth": false,
																"bPaginate" : false,
																"bLengthChange": false,
																"bInfo": false,
																"bFilter": false,
																"aoColumns": [
																	{ "width": "30%" },
																	{ "width": "7.5%", "bSortable": false },
																	{ "width": "22.5%", "bSortable": false },
																	{ "width": "10%", "bSortable": false },
																	{ "width": "10%", "bSortable": false },
																	{ "width": "10%", "bSortable": false },
																	{ "width": "10%", "bSortable": false }
																]
															});
															enablePopoversAndTooltips();
															getReadyToSend(nalogId, candidateId);
														}
													}else{
														if($.fn.DataTable.isDataTable('#requiredDocuments') == false){
															$('#requiredDocuments').DataTable({
																responsive: false,
																"order": [[ 0, "desc" ]],
																"bAutoWidth": false,
																"bPaginate" : false,
																"bLengthChange": false,
																"bInfo": false,
																"bFilter": false,
																"aoColumns": [
																	{ "width": "30%"},
																	{ "width": "7.5%", "bSortable": false },
																	{ "width": "22.5%", "bSortable": false },
																	{ "width": "10%", "bSortable": false },
																	{ "width": "10%", "bSortable": false },
																	{ "width": "10%", "bSortable": false },
																	{ "width": "10%", "bSortable": false }
																]
															});
														}
													}
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
										};
										$(document).ready(function() {
											var idCandidate = parseInt("<?php echo $kandidat_id; ?>");
											var idNalog = parseInt("<?php echo $candidateNalog; ?>");
											getRequiredDocuments(idNalog, idCandidate);
											
										});
									</script>
									<div class = "row pt-3">
										<div class = "col-12 scrollBarHorizontal" style="overflow-y: auto !important; user-select: none;">
											<table id="requiredDocuments" class="display" cellspacing="0" width = "100%">
												
											</table>
											
											<?php 
												include("includes/documents/modalDocuments.php");
											?>
										</div>
									</div>
									<!-- 
										*************************************************************
										*				DOKUMENTI PROCESA ODLASKA END				*
										*************************************************************
									-->
									<?php 
										}else{
									?>
									<div class = "row pt-3">
										<div class = "col-12" >
											<div class="alert alert-warning text-center" role="alert">
												<?php echo $txtArray["U procesu prikupljanja dokumenata niste zaduženi ni za jedan dokument"][$languageUser]."!"; ?>
											</div>
										</div>
									</div>
									<?php 
										}
									?>
								</div>
							</div>
						</div>
						<?php 
							}
						?>
						<!-- Ovdje je potrebno napraviti uslov koji ce odrediti u kojim segmentima procesa će biti dole naslovljeni dio vidljiv -->
						<!-- 
							********************************************************************************************************************************************************************
							****															DOKUMENTI PROCESA ODLASKA END																	****
							********************************************************************************************************************************************************************
						-->
						<div class = "row pt-3">
							<div class = "col-12">
								<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
									<div class = "row">
										<div class = "col-12">
											<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Edukacija"][$languageUser]; ?></p>
										</div>
									</div>
									<div class = "row pt-3">
										<div class = "col-12" style="overflow-y: auto !important;">
											<?php 
												$queryInfoEducation = $db->prepare("
													SELECT 
														ke_id, ke_smjer_id, ke_datumod, ke_datumdo, ke_naziv, ke_grad, ke_opis, ke_orderid, ke_vrsta_obrazovanja, ke_aktuelno
													FROM 
														idk_kandidat_edukacija
													WHERE 
														ke_kandidat_id = :ke_kandidat_id
														AND 
														ke_prikaz_pp = 1
													ORDER BY 
														ke_orderid 
													DESC
												");
												$queryInfoEducation->execute(array(
													':ke_kandidat_id' => $kandidat_id
												));
											?>
											<script>
												$(document).ready(function() {
													$('#tableEducation').DataTable({
														responsive: false,
														"order": [[ 1, "desc" ]],
														"bAutoWidth": false,
														"bPaginate" : false,
														"bLengthChange": false,
														"bInfo": false,
														"bFilter": false
													});
												});
											</script>
											<table id="tableEducation" class="display" cellspacing="0" width = "100%">
												<thead>
													<tr>
														<th class="text-center"><?php echo $txtArray["Naziv kvalifikacije"][$languageUser]; ?></th>
														<th class="text-center"><?php echo $txtArray["Od"][$languageUser]; ?></th>
														<th class="text-center"><?php echo $txtArray["Do"][$languageUser]; ?></th>
														<th class="text-center"><?php echo $txtArray["Grad"][$languageUser]; ?></th>
													</tr>
												</thead>
												<tbody>
													<?php 
														while($rowInfoEducation = $queryInfoEducation->fetch()){
															$ke_id = $rowInfoEducation['ke_id'];
															$ke_datumod = $rowInfoEducation['ke_datumod'];
															$keDatumOdOrder = "";
															if($ke_datumod == null){
																$ke_datumod_f = "-";
																$keDatumOdOrder = date('Y-m');
															}else{
																$ke_datumod_f = date('m.Y', strtotime($ke_datumod));
																$keDatumOdOrder = date('Y-m', strtotime($ke_datumod));
															}
															$ke_datumdo = $rowInfoEducation['ke_datumdo'];
															$ke_naziv_kvalifikacije = getSmjerNaziv($rowInfoEducation['ke_smjer_id'], $languageUser);
															$ke_naziv = $rowInfoEducation['ke_naziv'];
															$ke_grad = $rowInfoEducation['ke_grad'];
															$ke_opis = $rowInfoEducation['ke_opis'];
															$ke_vrsta_obrazovanja = $rowInfoEducation['ke_vrsta_obrazovanja'];
															$keDatumDoOrder = "";
															$ke_aktuelno = $rowInfoEducation['ke_aktuelno'];

															if($ke_aktuelno != 1){
																if($ke_datumdo == null){
																	$ke_datumdo_f = "-";
																	$keDatumDoOrder = date('Y-m');
																}else{
																	$ke_datumdo_f = date('m.Y', strtotime($ke_datumdo));
																	$keDatumDoOrder = date('Y-m', strtotime($ke_datumdo));
																}
															}else{
																$ke_datumdo_f = $txtArray["Aktuelno"][$languageUser];
																$keDatumDoOrder = date('Y-m');
															}
															if($ke_vrsta_obrazovanja == null){
																$ke_vrsta_obrazovanja = "-";
															}
													?>
													<tr>
														<td class="text-center"><?php echo $ke_naziv_kvalifikacije; ?></td>
														<td class="text-center" data-order="<?php echo strtotime($keDatumOdOrder); ?>"><?php echo $ke_datumod_f; ?></td>
														<td class="text-center" data-order="<?php echo strtotime($keDatumDoOrder); ?>"><?php echo $ke_datumdo_f; ?></td>
														<td class="text-center"><?php echo $ke_grad; ?></td>
													</tr>
													<?php 
														}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class = "row pt-3">
							<div class = "col-12">
								<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
									<div class = "row">
										<div class = "col-12">
											<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Radno iskustvo"][$languageUser]; ?></p>
										</div>
									</div>
									<div class = "row pt-3">
										<div class = "col-12" style="overflow-y: auto !important;">
											<?php 
												if($languageUser == 1){
													$conditionWorkExp = "kri_pozicija_de AS kriPozicija , kri_naziv AS kriNaziv, ";
												}elseif($languageUser == 0){
													$conditionWorkExp = "kri_pozicija AS kriPozicija , kri_naziv AS kriNaziv, ";
												}else{
													$conditionWorkExp = "kri_pozicija_en AS kriPozicija , kri_naziv AS kriNaziv, ";
												}
												$queryInfoWorkExperience = $db->prepare("
													SELECT 
														kri_id, kri_darum_od, kri_datum_do, ".$conditionWorkExp." kri_grad, kri_opis, kri_aktuelno
													FROM 
														idk_kandidat_radno_iskustvo
													WHERE 
														kri_kandidat_id = :kri_kandidat_id
														AND 
														kri_prikaz_pp = 1
													ORDER BY 
														kri_order 
													DESC
												");
												$queryInfoWorkExperience->execute(array(
													':kri_kandidat_id' => $kandidat_id
												));
											?>
											<script>
												$(document).ready(function() {
													$('#tableWorkExperience').DataTable({
														responsive: false,
														"order": [[ 3, "desc" ]],
														"bAutoWidth": false,
														"bPaginate" : false,
														"bLengthChange": false,
														"bInfo": false,
														"bFilter": false
													});
												});
											</script>
											<table id="tableWorkExperience" class="display" cellspacing="0" width = "100%">
												<thead>
													<tr>
														<th class="text-center"><?php echo $txtArray["Pozicija"][$languageUser]; ?></th>
														<th class="text-center"><?php echo $txtArray["Poslodavac"][$languageUser]; ?></th>
														<th class="text-center"><?php echo $txtArray["Grad"][$languageUser]; ?></th>
														<th class="text-center"><?php echo $txtArray["Od"][$languageUser]; ?></th>
														<th class="text-center"><?php echo $txtArray["Do"][$languageUser]; ?></th>
													</tr>
												</thead>
												<tbody>
													<?php 
														while($rowInfoWorkExperience = $queryInfoWorkExperience->fetch()){
															$kri_id = $rowInfoWorkExperience['kri_id'];
															$kri_darum_od = $rowInfoWorkExperience['kri_darum_od'];
															$kri_darum_od_f = date('m.Y', strtotime($kri_darum_od));
															$kriDatumOdOrder = date("Y-m", strtotime($kri_darum_od));
															$kri_datum_do = $rowInfoWorkExperience['kri_datum_do'];
															$kri_pozicija = $rowInfoWorkExperience['kriPozicija'];
															$kri_naziv = $rowInfoWorkExperience['kriNaziv'];
															$kri_grad = $rowInfoWorkExperience['kri_grad'];
															$kri_opis = $rowInfoWorkExperience['kri_opis'];
															$kriDatumDoOrder = "";
															$kri_aktuelno = $rowInfoWorkExperience['kri_aktuelno'];

															if($kri_aktuelno != 1){
																if($kri_datum_do == null){
																	$kri_datum_do_f = $txtArray["Aktuelno"][$languageUser];
																	$kriDatumDoOrder = date("Y-m");
																}else{
																	$kri_datum_do_f = date('m.Y', strtotime($kri_datum_do));
																	$kriDatumDoOrder = date("Y-m", strtotime($kri_datum_do));
																}
															}else{
																$kri_datum_do_f = $txtArray["Aktuelno"][$languageUser];
																$kriDatumDoOrder = date("Y-m");
															}
													?>
													<tr>
														<td class="text-center"><?php echo $kri_pozicija; ?></td>
														<td class="text-center"><?php echo $kri_naziv; ?></td>
														<td class="text-center"><?php echo $kri_grad; ?></td>
														<td class="text-center" data-order = "<?php echo strtotime($kriDatumOdOrder); ?>"><?php echo $kri_darum_od_f; ?></td>
														<td class="text-center" data-order = "<?php echo strtotime($kriDatumDoOrder); ?>"><?php echo $kri_datum_do_f; ?></td>
													</tr>
													<?php 
														}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--<div class = "row pt-3">
							<div class = "col-12">
								<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
									<div class = "row">
										<div class = "col-12">
											<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Komentar o radniku"][$languageUser]; ?></p>
										</div>
									</div>
									<div class = "row pt-3">
										<div class = "col-12" >
											<p class = "mb-0 text-break lh-sm" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;">Komentar o radniku</p>
										</div>
									</div>
								</div>
							</div>
						</div>-->
						<div class = "row pt-3">
							<div class = "col-12">
								<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
									<div class = "row">
										<div class = "col-12">
											<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Status kandidata"][$languageUser]; ?></p>
										</div>
									</div>
									<div class = "row pt-3">
										<div class = "col-12">
											<!--<div class="position-relative m-4">
												<div class="progress" style="height: 2px;">
													<div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
												</div> 
												<button type="button" class="shadow-sm position-absolute top-0 start-0 translate-middle btn btn-sm btn-success disabled rounded-pill" style="width: 2rem; height:2rem;"><i class="fa fa-check" aria-hidden="true"></i></button>
												<button type="button" class="shadow-sm position-absolute top-0 start-25 translate-middle btn btn-sm btn-success disabled rounded-pill" style="width: 2rem; height:2rem;"><i class="fa fa-check" aria-hidden="true"></i></button>
												<button type="button" class="shadow-sm position-absolute top-0 start-50 translate-middle btn btn-sm btn-success rounded-pill" style="width: 2rem; height:2rem;"><i class="fa fa-check" aria-hidden="true"></i></button>
												<button type="button" class="shadow-sm position-absolute top-0 start-75 translate-middle btn btn-sm btn-success disabled rounded-pill" style="width: 2rem; height:2rem;"><i class="fa fa-check" aria-hidden="true"></i></button>
												<button type="button" class="shadow-sm position-absolute top-0 start-100 translate-middle btn btn-sm btn-success disabled rounded-pill" style="width: 2rem; height:2rem;"><i class="fa fa-check" aria-hidden="true"></i></button>
											</div>-->
											<style>
												.cekaUgovor{
													background-color: #b3d3e5;
												}
												.poslanUgovor{
													background-color: #7ab6d9;
												}
												.potpisanUgovor{
													background-color: #1d84c0;
												}
												.poceoRaditi{
													background-color: #79b465;
												}
												.casting{
													background-color: #7ab6d9;
												}
												.intervju{
													background-color: #fdd878;
												}
												.odbijen{
													background-color: #eb8888;
												}
												.prihvacen{
													background-color: #79b465;
												}
												.progBarStyle{
													background-color: #e9ecef !important;
												}
											</style>
											<?php
											// var_dump($bar_id);
												if($type == 1){
											?>
											<div class="row align-items-center text-center progBarStyle rounded-3"> 
												<div class="rounded-3 col-lg-3 <?php echo (($bar_id == 2) ? "casting text-white fs-5":"text-dark"); ?> py-2 px-2"><?php echo $txtArray["Casting"][$languageUser]; ?></div>
												<div class="rounded-3 col-lg-3 <?php echo (($bar_id == 3) ? "intervju text-white fs-5":"text-dark"); ?> py-2 px-2"><?php echo $txtArray["Intervju"][$languageUser]; ?></div>
												<div class="rounded-3 col-lg-3 <?php echo (($bar_id == 5) ? "odbijen text-white fs-5":"text-dark"); ?> py-2 px-2"><?php echo $txtArray["Odbijen"][$languageUser]; ?></div>
												<div class="rounded-3 col-lg-3 <?php echo (($bar_id == 4) ? "prihvacen text-white fs-5":"text-dark"); ?> py-2 px-2"><?php echo $txtArray["Prihvaćen"][$languageUser]; ?></div>
											</div>
											<?php 
												}else{
											?>
											<div class="row align-items-center text-center progBarStyle rounded-3">
												<div class="s7  rounded-3 col-lg-3 <?php echo (($bar_id == 2) ? "cekaUgovor text-white fs-5":"text-dark"); ?> py-2 px-2"><?php echo $txtArray["Čeka ugovor"][$languageUser]; ?></div>
												<div class="s8  rounded-3 col-lg-3 <?php echo (($bar_id == 3) ? "poslanUgovor text-white fs-5":"text-dark"); ?> py-2 px-2"><?php echo $txtArray["Poslan ugovor"][$languageUser]; ?></div>
												<div class="s9  rounded-3 col-lg-3 <?php echo (($bar_id == 4) ? "potpisanUgovor text-white fs-5":"text-dark"); ?> py-2 px-2"><?php echo $txtArray["Potpisan ugovor"][$languageUser]; ?></div>
												<div class="s10 rounded-3 col-lg-3 <?php echo (($bar_id == 5) ? "poceoRaditi text-white fs-5":"text-dark"); ?> py-2 px-2"><?php echo $txtArray["Počeo raditi"][$languageUser]; ?></div>
											</div>
											<?php 
												}
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class = "row pt-3">
							<div class = "col-12">
								<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
									<div class = "row align-items-center">
										<div class = "col-12">
											<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Detalji o intervjuu"][$languageUser]; ?></p>
										</div>
									</div>
									<div class = "row pt-3">
										<div class = "col-12 px-5">
											
											<?php 
												$appointmentsCand = array();
												$appointmentsCand = getCandidatAppointmentsArrayR($kandidat_id, $candidateNalog);
												if(count($appointmentsCand["count"]) != 0){
													foreach($appointmentsCand["count"] AS $countAppointments){
											?>
														<!-- JEDAN TERMIN START -->
														<div class = "row mb-3 align-items-center shadow-sm rounded text-center px-3 py-3">
															<div class = "col-lg-8">
																<div class = "row">
																	<div class = "col-md-4">
																		<div class = "row align-items-center text-center">
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Grad"][$languageUser]; ?> #1</p>
																			</div>
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;"><?php echo $appointmentsCand["pap_city"][$countAppointments]; ?></p>
																			</div>
																		</div>
																	</div>
																	<div class = "col-md-4">
																		<div class = "row align-items-center text-center"> 
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Datum"][$languageUser]; ?> #1</p>
																			</div>
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;"><?php echo $appointmentsCand["pap_date"][$countAppointments]; ?></p>
																			</div>
																		</div>
																	</div>
																	<div class = "col-md-4">
																		<div class = "row align-items-center text-center">
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Vrijeme"][$languageUser]; ?> #1</p>
																			</div>
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;"><?php echo $appointmentsCand["pca_time"][$countAppointments]; ?></p>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<?php 
																// if(1 == 1){
																if($appointmentsCand["pca_avg_rating"][$countAppointments] != "noRating"){ 
																// Odkomentarisi kad zavrsis backend za povezivanje partnerom
																	
															?>
															<?php 
																$classLgForClickInsert = "col-lg-4";
																if($bar_id == 3 && $type == 1 && $appointmentsCand["pca_status"][$countAppointments] == 1){
																	$classLgForClickInsert = "col-lg-2";
															?>
															<div class = "col-lg-2">
																<div class = "row align-items-center text-center">
																	<div class = "col-6">
																		<button type="button" class="btn btn-success" data-bs-toggle="modal" href="#modalAccept" role="button"><i class="fa fa-check-circle-o" aria-hidden="true"></i></button>
																	</div>
																	<div class = "col-6">
																		<button type="button" class="btn btn-danger" data-bs-toggle="modal" href="#modalOdbij" role="button"><i class="fa fa-times-circle-o" aria-hidden="true"></i></button>
																	</div>
																</div>
															</div>
															<div class="modal fade" id="modalOdbij" aria-hidden="true" aria-labelledby="modalOdbijLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
																<div class="modal-dialog modal-dialog-centered">
																	<div class="modal-content border-0">
																		<div class="modal-header border-bottom-0 text-center">
																			<h5 class="modal-title w-100" id="modalOdbijLabel"><?php echo $txtArray["Odbij kandidata"][$languageUser]; ?></h5>
																			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																		</div>
																		<div class="modal-body">
																			<div class = "row pt-1 my-3">
																				<div class = "col-12 text-center">
																					<div class="mb-5 mx-5 row">
																						<div class="col-sm-12">
																							<div id = "razlogOdbijanjaIdEffect">
																							<select class="selectpicker form-control" id="razlogOdbijanjaId" placeholder = "<?php echo $txtArray["Odaberite razlog odbijanja"][$languageUser]; ?>">
																								<?php 
																									$reject_resons = getRejectReasonsArrayR($languageUser);
																									foreach($reject_resons["count"] AS $rrCnt){
																										echo '<option value = "'.$reject_resons["id"][$rrCnt].'">'.$reject_resons["name"][$rrCnt].'</option>';
																									}
																									unset($reject_resons);
																								?>
																							</select>
																							<script>
																								$(document).ready(function() {
																									$('#razlogOdbijanjaId').selectpicker();
																								});
																							</script>
																							</div>
																						</div>
																					</div>
																					<!--<div class="mb-3 mx-5 row">
																						<div class="col-sm-12">
																							<textarea id="razlog_odbijanja" class="textArreaStyle" rows="2" maxlength="200" placeholder="<?php //echo $txtArray["Unesite razlog odbijanja kandidata"][$languageUser]; ?>"></textarea>
																						</div>
																					</div>-->
																				</div>
																				<div class = "col-12 text-center">
																					<p  style = "color: #8E97A3; font-weight: 600; font-size:16px;" class = "mb-0">
																						<?php echo $txtArray["Jeste li sigurni da odbijate ovog kandidata?"][$languageUser]; ?>
																					</p>
																				</div>
																			</div>
																		</div>
																		<div class="modal-footer justify-content-center border-top-0">
																			<button class="btn btn-danger dugmeOdbijamKan"  data-kan_id_odbijam = "<?php echo $kandidat_key; ?>" data-nalog_id_odbijam = "<?php echo $candidateNalog; ?>" data-nalog_termin_id="<?php echo $appointmentsCand["pap_id"][$countAppointments];?>"><?php echo $txtArray["Odbijam"][$languageUser]; ?></button>
																		</div>
																	</div>
																</div>
															</div>
															<div class="modal fade" id="modalAccept" aria-hidden="true" aria-labelledby="modalAcceptLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
																<div class="modal-dialog modal-dialog-centered">
																	<div class="modal-content border-0">
																		<div class="modal-header border-bottom-0 text-center">
																			<h5 class="modal-title w-100" id="modalAcceptLabel"><?php echo $txtArray["Prihvati kandidata"][$languageUser]; ?></h5>
																			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																		</div>
																		<div class="modal-body">
																			<div class = "row pt-1 my-3">
																				<div class = "col-12 text-center">
																					<p  style = "color: #8E97A3; font-weight: 600; font-size:16px;" class = "mb-0">
																						<?php echo $txtArray["Jeste li sigurni da prihvatate ovog kandidata?"][$languageUser]; ?>
																					</p>
																				</div>
																			</div>
																		</div>
																		<div class="modal-footer justify-content-center border-top-0">
																			<button class="btn btn-success dugmePrihvatamKan" role="button" data-kan_id_prihvatam = "<?php echo $kandidat_key; ?>" data-nalog_id_prihvatam = "<?php echo $candidateNalog; ?>"><?php echo $txtArray["Prihvatam"][$languageUser]; ?></button>
																		</div>
																	</div>
																</div>
															</div>
															
															<?php 
																}
															?>
															<div class = "<?php echo $classLgForClickInsert; ?> cursorPointer clickviewInterview" data-case_interview = "<?php echo ( ($flagEnpalAccess == 1) ? 'candidateViewInterviewEnpal' : 'candidateViewInterview' ); ?>" data-pap_id = "<?php echo $appointmentsCand["pap_id"][$countAppointments];?>" data-pca_id = "<?php echo $appointmentsCand["pca_id"][$countAppointments];?>">
																<i class="fa fa-angle-right fa-2x" aria-hidden="true"></i>
															</div>
															<?php 
																}else{
																	if($appointmentsCand["pca_status"][$countAppointments] == 1){
															?>
															
															<div class = "col-lg-4 cursorPointer clickInesertInterview" data-case_interview = "<?php echo ( ($flagEnpalAccess == 1) ? 'candidateInsertInterviewEnpal' : 'candidateInsertInterview' ); ?>" data-pap_id = "<?php echo $appointmentsCand["pap_id"][$countAppointments];?>" data-pca_id = "<?php echo $appointmentsCand["pca_id"][$countAppointments];?>">
																<div class="spinner-border" role="status">
																  <span class="visually-hidden">Loading...</span>
																</div>
															</div>	
															<?php
																	}else{
															?>
															<div class = "col-lg-4 cursorPointer clickviewInterview" data-case_interview = "<?php echo ( ($flagEnpalAccess == 1) ? 'candidateViewInterviewEnpal' : 'candidateViewInterview' ); ?>" data-pap_id = "<?php echo $appointmentsCand["pap_id"][$countAppointments];?>" data-pca_id = "<?php echo $appointmentsCand["pca_id"][$countAppointments];?>">
																<i class="fa fa-angle-right fa-2x" aria-hidden="true"></i>
															</div>
															<?php 
																	}
																}
															?>
														</div>
														<!-- JEDAN TERMIN END -->
											<?php 
													}
											?>
													<script>
														//$(document).unbind('click',"#clickviewInterview");
														$(".clickviewInterview").on("click", function() {
															getLoaderBig();
															var clickKanIdVI = parseInt("<?php echo $kandidat_id; ?>");
															var type = parseInt("<?php echo $type; ?>");
															var bar_id = parseInt("<?php echo $bar_id; ?>");
															var nalog_id = parseInt("<?php echo $candidateNalog; ?>");
															var pap_id = parseInt($(this).data("pap_id"));
															var pca_id = parseInt($(this).data("pca_id"));
															var case_interview = $(this).data("case_interview");
															$.ajax({
																url: `ajax.php?action=${case_interview}`,
																type: 'POST',
																data: {
																	'kandidat_id':clickKanIdVI,
																	'pap_id':pap_id,
																	'pca_id':pca_id,
																	'type':type,
																	'bar_id':bar_id,
																	'nalog_id':nalog_id
																},
																dataType: 'html',
																success: function(data){
																	// $("#candidateViewInterview").empty().append(data);
																	$("#candidateViewInterview").html(data);
																	$( "#candidateProfile" ).hide( 'slide', 500, function(){
																		$( "#candidateProfile" ).html('');
																		$( "#candidateViewInterview" ).show( 'slide', 1000);
																	});
																	removeLoader();
																},
																error: function (xhr, ajaxOptions, thrownError) {
																	alert(xhr.status);
																	alert(thrownError);
																}
															});
														});
														//$(document).unbind('click',"#clickInesertInterview");
														// $("#clickInesertInterview").unbind('click');
														$(".clickInesertInterview").on("click",function() {
															var flagEnpalAccess = parseInt("<?php echo $flagEnpalAccess; ?>");
															var clickKanIdVI = parseInt("<?php echo $kandidat_id; ?>");
															var clickKanIdKey = "<?php echo $kandidat_key; ?>";
															var type = parseInt("<?php echo $type; ?>");
															var bar_id = parseInt("<?php echo $bar_id; ?>");
															var nalog_id = parseInt("<?php echo $candidateNalog; ?>");
															var pap_id = parseInt($(this).data("pap_id")); 
															var pca_id = parseInt($(this).data("pca_id"));
															var case_interview = $(this).data("case_interview");
															//console.log(clickKanIdKey);
															
															if ( flagEnpalAccess == 1 ) {
																getLoaderBig();
																$.ajax({
																	url: `ajax.php?action=${case_interview}`,
																	type: 'POST',
																	data: {
																		'kandidat_id':clickKanIdVI,
																		'pap_id':pap_id,
																		'pca_id':pca_id,
																		'type':type,
																		'bar_id':bar_id,
																		'nalog_id':nalog_id
																	},
																	dataType: 'html',
																	success: function(data){
																		$("#candidateInsertInterview").html(data);
																		$( "#candidateProfile" ).hide( 'slide', 250, function(){
																			$( "#candidateProfile" ).html('');
																			$( "#candidateInsertInterview" ).show( 'slide', 250);
																		});
																		removeLoader();
																	},
																	error: function (xhr, ajaxOptions, thrownError) {
																		alert(xhr.status);
																		alert(thrownError);
																	}
																});
															} else {

																getAccessControl(nalog_id, clickKanIdKey, null, 1, function(response_access_control){
																	var reminder_status 			= response_access_control['status'];
																	var reminder_id 				= response_access_control['id'];
																	var reminder_assigned_user 		= response_access_control['assigned'];
																	var reminder_is_logged_user 	= response_access_control['isLogged'];
																	var reminder_response_message	= response_access_control['message'];
																	var reminder_response_title		= response_access_control['title'];
																	
																	if((reminder_status == 101) || reminder_status == 1 || (reminder_status == 2 && reminder_is_logged_user == 1)){
																		//alert('Uradi sve normalno');
																		getLoaderBig();
																		$.ajax({
																			url: `ajax.php?action=${case_interview}`,
																			type: 'POST',
																			data: {
																				'kandidat_id':clickKanIdVI,
																				'pap_id':pap_id,
																				'pca_id':pca_id,
																				'type':type,
																				'bar_id':bar_id,
																				'nalog_id':nalog_id
																			},
																			dataType: 'html',
																			success: function(data){
																				$("#candidateInsertInterview").html(data);
																				$( "#candidateProfile" ).hide( 'slide', 250, function(){
																					$( "#candidateProfile" ).html('');
																					$( "#candidateInsertInterview" ).show( 'slide', 250);
																				});
																				removeLoader();
																			},
																			error: function (xhr, ajaxOptions, thrownError) {
																				alert(xhr.status);
																				alert(thrownError);
																			}
																		});
																		if(reminder_status == 1){
																			updateReminderStatus(reminder_id, 2, function(response_update_reminder){
																				var update_status 	= response_update_reminder["status"];
																				var update_message 	= response_update_reminder["message"];
																				if(update_status == 2){
																					showToast(update_message, reminder_response_title);
																				}
																			});
																		}
																	}
																	else if(reminder_status == 104){
																		showToast(reminder_response_message, reminder_response_title);
																	}
																	else{
																		showToast(reminder_response_message, reminder_response_title);
																	}
																});
															}
														});
													</script>
											<?php
												}else{
													echo "";
												}
												unset($appointmentsCand);
												
											?>
											
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--  	>>>		Ostali termini za kandidata START 	<<<		-->
						<div class = "row pt-3">
							<div class = "col-12">
								<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
									<div class = "row align-items-center">
										<div class = "col-12">
											<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Detalji ostalih intervjua"][$languageUser]; ?></p>
										</div>
									</div>
									<div class = "row pt-3">
										<div class = "col-12 px-5">
											<?php 
												$otherAppointmentsCand = array();
												$otherAppointmentsCand = getOtherAppointmentsOfCandidateArrayR($kandidat_id, $candidateNalog); 
												if(count($otherAppointmentsCand["count"]) != 0){
													foreach($otherAppointmentsCand["count"] AS $countOtherAppointments){
											?>
														<!-- JEDAN TERMIN START -->
														<div class = "row mb-3 align-items-center shadow-sm rounded text-center px-3 py-3">
															<div class = "col-lg-10">
																<div class = "row">
																	<div class = "col-md-3">
																		<div class = "row align-items-center text-center">
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Nalog"][$languageUser]; ?> #1</p>
																			</div>
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;"><?php echo getNazivNalogaR($otherAppointmentsCand["pap_nalog_id"][$countOtherAppointments]); ?></p>
																			</div>
																		</div>
																	</div>
																	<div class = "col-md-3">
																		<div class = "row align-items-center text-center">
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Grad"][$languageUser]; ?> #1</p>
																			</div>
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;"><?php echo $otherAppointmentsCand["pap_city"][$countOtherAppointments]; ?></p>
																			</div>
																		</div>
																	</div>
																	<div class = "col-md-3">
																		<div class = "row align-items-center text-center"> 
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Datum"][$languageUser]; ?> #1</p>
																			</div>
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;"><?php echo $otherAppointmentsCand["pap_date"][$countOtherAppointments]; ?></p>
																			</div>
																		</div>
																	</div>
																	<div class = "col-md-3">
																		<div class = "row align-items-center text-center">
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Vrijeme"][$languageUser]; ?> #1</p>
																			</div>
																			<div class = "col-12">
																				<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;"><?php echo $otherAppointmentsCand["pca_time"][$countOtherAppointments]; ?></p>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class = "col-lg-2 cursorPointer clickViewOtherInterview" data-pap_id_voi = "<?php echo $otherAppointmentsCand["pap_id"][$countOtherAppointments];?>" data-pca_id_voi = "<?php echo $otherAppointmentsCand["pca_id"][$countOtherAppointments];?>">
																<i class="fa fa-angle-right fa-2x" aria-hidden="true"></i>
															</div>
														</div>
														<!-- JEDAN TERMIN END -->
											<?php 
													}
											?>
												<script>
													//$(document).unbind('click',"#clickviewInterview");
													$(".clickViewOtherInterview").on("click", function() {
														getLoaderBig();
														var canIdVOI = parseInt("<?php echo $kandidat_id; ?>");
														var typeVOI = parseInt("<?php echo $type; ?>");
														var barIdVOI = parseInt("<?php echo $bar_id; ?>");
														var nalogIdVOI = parseInt("<?php echo $candidateNalog; ?>");
														var papIdVOI = parseInt($(this).data("pap_id_voi"));
														var pcaIdVOI = parseInt($(this).data("pca_id_voi"));
														$.ajax({
															url: 'ajax.php?action=candidateViewInterview',
															type: 'POST',
															data: {
																'kandidat_id':canIdVOI,
																'pap_id':papIdVOI,
																'pca_id':pcaIdVOI,
																'type':typeVOI,
																'bar_id':barIdVOI,
																'nalog_id':nalogIdVOI
															},
															dataType: 'html',
															success: function(data){
																// $("#candidateViewInterview").empty().append(data);
																$("#candidateViewInterview").html(data);
																$( "#candidateProfile" ).hide( 'slide', 500, function(){
																	$( "#candidateProfile" ).html('');
																	$( "#candidateViewInterview" ).show( 'slide', 1000);
																});
																removeLoader();
															},
															error: function (xhr, ajaxOptions, thrownError) {
																alert(xhr.status);
																alert(thrownError);
															}
														});
													});
												</script>
											<?php 
												}else{
													echo "";
												}
												unset($otherAppointmentsCand);

											?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- 	>>>		Ostali termini za kandidata END    	<<<		-->
					</div>
				</div>
			</div>
		</div>
	<?php
	break;

	case "candidateViewInterviewEnpal":
		$kandidat_id 	= $_POST["kandidat_id"];
		$pap_id 		= $_POST["pap_id"];
		$pca_id 		= $_POST["pca_id"];
		$type 			= $_POST["type"];
		$bar_id 		= $_POST["bar_id"];
		$nalog_id 		= $_POST["nalog_id"];
		$candidate_key 	= getCandidateKeyById($kandidat_id);
	?>
		<div class = "row">
				<div class = "col-12">
					<div class = "row align-items-center">
						<div class = "col-1 text-center goToProfile1 cursorPointer">
							<i class="fa fa-angle-left fa-3x" aria-hidden="true"></i>
						</div>
						<div class = "col-11 text-center">
							<p class = "text_color mb-0" style = "font-style: normal; font-weight: bold; font-size: 28px;"><?php echo $txtArray["Detalji o intervjuu"][$languageUser]; ?></p>
						</div>
					</div>
					<script>
						$('.goToProfile1').on('click', function() {
							getLoaderBig();
							var readyKanId = "<?php echo $candidate_key; ?>";
							var type = parseInt("<?php echo $type; ?>");
							var bar_id = parseInt("<?php echo $bar_id; ?>");
							var nalog_id = parseInt("<?php echo $nalog_id; ?>");
							$.ajax({
								url: 'ajax.php?action=candidateProfile',
								type: 'POST',
								data: {
									'kandidat_id':readyKanId,
									'type':type,
									'bar_id':bar_id,
									'nalog_id':nalog_id
								},
								dataType: 'html',
								success: function(data){
									$("#candidateProfile").html(data);
									$( "#candidateViewInterview" ).hide( 'slide', 500, function(){
										$( "#candidateViewInterview" ).html( '' );
										$( "#candidateProfile" ).show( 'slide', 1000);
										$("#edit_candidate_partner_data").unbind('click').bind('click', handleEditCandidatePartnerData);
									});
									removeLoader();
								},
								error: function (xhr, ajaxOptions, thrownError) {
									alert(xhr.status);
									alert(thrownError);
								}
							});
						});
					</script>
					<div class = "row gx-5 gy-3">
						<div class = "col-md-12 ">
							<div class = "row">
								<div class = "col-12">
									<div class = "px-5" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
										<!--
											General info START
											-->
												<?php 
													//Provjera da li je vec odgovoreno
													$flagInfoEnpal = 0;
													$infoEnpalPCA = getInfoEnpalPCAArrayR($kandidat_id, $pca_id);
													if($infoEnpalPCA["status"] == 1){
														$flagInfoEnpal = 1;
													}
												?>

												<div class = "row">
													<div class = "col-12">
														<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Intervjuer"][$languageUser]; ?></p>
													</div>
												</div>
												<div class = "row pt-3">
													<div class = "col-lg-12">
														<div class = "row ">
															<div class = "col-12">
																<textarea class="textArreaStyle" rows="1" disabled><?php echo ( ($flagInfoEnpal == 1 AND $infoEnpalPCA["pca_interviewer"] != '') ? $infoEnpalPCA["pca_interviewer"] : null  );?></textarea>
															</div>
														</div>
													</div>
												</div>
											<!--
											General info END
										-->
										<!--
											Pitanja i ocjenjivanje START
											-->
												<div class = "row pt-5">
													<div class = "col-12">
														<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Pitanja i ocjenjivanje"][$languageUser]; ?></p>
													</div>
												</div>
												<div class = "row pt-3">
													<div class = "col-12">
														<!-- 
															Pitanja Start
															-->
																<?php 
																	$appQuestions = array();
																	$appQuestions =  getAppointmentQuestionForAppointmentEnpalArrayR($pap_id);

																	if(count($appQuestions["count"]) != 0){
																		foreach($appQuestions["count"] AS $countAppQuestions){
																			?>

																				<!--
																					Jedna grupa Start 
																					-->
																						<?php 
																							if ($appQuestions["categoryStart"][$countAppQuestions] == 1 AND $appQuestions["categoryId"][$countAppQuestions] != 0) {
																								?>
																									<hr class="border border-secondary border-2 opacity-50 mb-2 <?php echo ($countAppQuestions != 0) ? "mt-5" : "mt-2";?>">
																									<div class = "row px-3 py-3 mb-3">
																										<div class = "col-lg-12">
																											<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 19px;"><?php echo $appQuestions["categoryName"][$countAppQuestions];?></p>
																										</div>
																									</div>
																								<?php 
																							}
																						?>
																					<!--
																					Jedna grupa END 
																				-->

																				<!--
																					Jedno pitanje Start 
																					-->
																						<div class = "row px-3 py-3 mb-3 rounded shadow-sm">
																							<div class = "col-12">
																								<?php 
																									$classText = "";
																									$classRating = "";
																									$classDropdown = "";

																									if ($appQuestions["pqu_has_rating"][$countAppQuestions] == 1 AND $appQuestions["pqu_has_dropdown"][$countAppQuestions] == 1 ) {
																										$classText = "col-lg-6";
																										$classRating = "col-lg-3";
																										$classDropdown = "col-lg-3";
																									} else if ($appQuestions["pqu_has_rating"][$countAppQuestions] == 1 AND $appQuestions["pqu_has_dropdown"][$countAppQuestions] == 0) {
																										$classText = "col-lg-7";
																										$classRating = "col-lg-5";
																										$classDropdown = "col-lg";
																									} else if ($appQuestions["pqu_has_rating"][$countAppQuestions] == 0 AND $appQuestions["pqu_has_dropdown"][$countAppQuestions] == 1) {
																										$classText = "col-lg-7";
																										$classRating = "col-lg";
																										$classDropdown = "col-lg-5";
																									} else {
																										$classText = "col-lg-12";
																										$classRating = "col-lg";
																										$classDropdown = "col-lg";
																									}
																								?>
																								<div class = "row align-items-center my-2">
																									<!--
																										Text Pitanja Start
																										-->
																											<div class = "<?php echo $classText; ?>">
																												<div class = "row align-items-center">
																													<!--<div class = "col-12">
																														<p class = "mb-0" style = "color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php //echo $txtArray["Pitanje"][$languageUser]; ?> #<?php //echo $countAppQuestions + 1;?></p>
																													</div>-->
																													<div class = "col-12">
																														<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;"><?php echo $appQuestions["pqu_question"][$countAppQuestions];?></p>
																													</div>
																												</div>
																											</div>
																										<!--
																										Text Pitanja END
																									-->
																									<?php 
																										if($appQuestions["pqu_has_rating"][$countAppQuestions] == 1) {
																											?>
																												<!--
																													Ocjena Pitanja Start
																													-->
																														<div class = "<?php echo $classRating; ?>">
																															<div class = "row align-items-center">
																																<div class = "col-12 text-center">
																																	<?php 
																																		//Provjera da li je vec odgovoreno
																																		$ratingForQuestion = getRatingForAppointmentQuestion($kandidat_id, $appQuestions["papq_id"][$countAppQuestions]);
																																		$arrayCheckedValue = array(
																																			"1" => array("fa fa-star-o"),
																																			"2" => array("fa fa-star-o"),
																																			"3" => array("fa fa-star-o"),
																																			"4" => array("fa fa-star-o"),
																																			"5" => array("fa fa-star-o")
																																		);
																																		if($ratingForQuestion != 100 AND $ratingForQuestion != 101 AND $ratingForQuestion != 102){
																																			for($i = 1; $i <= $ratingForQuestion; $i++){
																																				$arrayCheckedValue[$i][0] = "fa fa-star";
																																			}
																																		}else{
																																			$arrayCheckedValue["1"][0] = "fa fa-star-o";
																																		}
																																	?>
																																	<ul class=" list-group list-group-horizontal justify-content-center">
																																		<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["1"][0]; ?> fa-2x" style = "color: #FDD878;" aria-hidden="true"></i></li>
																																		<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["2"][0]; ?> fa-2x" style = "color: #FDD878;" aria-hidden="true"></i></li>
																																		<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["3"][0]; ?> fa-2x" style = "color: #FDD878;" aria-hidden="true"></i></li>
																																		<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["4"][0]; ?> fa-2x" style = "color: #FDD878;" aria-hidden="true"></i></li>
																																	</ul>
																																	<?php 
																																		unset($arrayCheckedValue);
																																	?>
																																</div>
																															</div>
																														</div>
																													<!--
																													Ocjena Pitanja END
																												-->
																											<?php
																										}
																										
																										if($appQuestions["pqu_has_dropdown"][$countAppQuestions] == 1) {

																											$questionOptions = getQuestionOptionsArrayR($appQuestions["pqu_id"][$countAppQuestions]);
																											$valueOptionForQuestion = getOptionForAppointmentQuestion($kandidat_id, $appQuestions["papq_id"][$countAppQuestions]);

																											?>
																												<!--
																													Dropdown Pitanja Start
																													-->
																														<div class = "<?php echo $classDropdown; ?>">
																															<div class = "row align-items-center">
																																<div class = "col-12">
																																	<select class="selectpicker col-12 selectpicker_option" title="<?php echo $txtArray['Odaberite opciju'][$languageUser]; ?>">
																																		<?php 
																																			if (count($questionOptions["count"]) != 0) {

																																				foreach($questionOptions["count"] AS $countQuestionOptions) {

																																					?>

																																						<option 
																																							value="<?php echo $questionOptions["pqo_value"][$countQuestionOptions]; ?>" 
																																							<?php echo ( ($questionOptions["pqo_value"][$countQuestionOptions] == $valueOptionForQuestion ) ? 'selected' : ''); ?>
																																							<?php echo ( ($questionOptions["pqo_value_subtext"][$countQuestionOptions] != NULL) ? 'data-subtext="'.$questionOptions["pqo_value_subtext"][$countQuestionOptions].'"' : '' ); ?>
																																						>
																																							<?php echo $questionOptions["pqo_value_text"][$countQuestionOptions]; ?>
																																						</option>
																																					
																																					<?php

																																				}

																																			}
																																		?>
																																	</select>
																																</div>
																															</div>
																														</div>
																													<!--
																													Dropdown Pitanja END
																												-->
																											<?php 
																										}
																									?>
																								</div>
																								<?php 
																									if($appQuestions["pqu_has_text"][$countAppQuestions] == 1) {
																										?>
																											<!--
																												Biljeska Pitanja Start
																												-->
																													<div class = "row align-items-center">
																														<div class = "col-lg-12">
																															<div class = "row align-items-center">
																																<div class = "col-12">
																																	<?php 
																																		//Provjera da li je vec odgovoreno
																																		$commentForQuestion = getCommentForAppointmentQuestion($kandidat_id, $appQuestions["papq_id"][$countAppQuestions]);
																																		if($commentForQuestion != 100 AND $commentForQuestion != 101 AND $commentForQuestion != 102){
																																			$commentForQuestion = $commentForQuestion;
																																		}else{
																																			$commentForQuestion = "";
																																		}
																																	?>
																																	<textarea class="textArreaStyle" rows="2" disabled><?php echo $commentForQuestion;?></textarea>
																																</div>
																															</div>
																														</div>
																													</div>
																												<!--
																												Biljeska Pitanja END
																											-->
																										<?php 
																									}
																								?>
																							</div>
																						</div>
																					<!--
																					Jedno pitanje END 
																				-->

																			<?php 
																		}
																	}

																	unset($appQuestions);
																?>
															<!-- 
															Pitanja End
														-->
													</div>
												</div>
											<!--
											Pitanja i ocjenjivanje END
										-->
										<!--
											General info START
											-->
												<?php  if($infoEnpalPCA["pca_comment"] != ''){
													?>
													<div class = "row pt-3">
														<div class = "col-12">
															<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Opšti komentar"][$languageUser]; ?></p>
														</div>
													</div>
													<div class = "row pt-3">
														<div class = "col-lg-12">
															<div class = "row ">
																<div class = "col-12">
																	<textarea class="textArreaStyle" rows="2" disabled><?php echo ( ($flagInfoEnpal == 1 AND $infoEnpalPCA["pca_comment"] != '') ? $infoEnpalPCA["pca_comment"] : null  );?></textarea>
																</div>
															</div>
														</div>
													</div>
													<?php 
												} ?>
												<div class = "row pt-3">
													<div class = "col-12">
														<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Preporuka"][$languageUser]; ?></p>
													</div>
												</div>
												<div class = "row pt-3">
													<div class = "col-lg-12">
														<div class = "row ">
															<div class = "col-12">
																<select class="selectpicker col-12 selectpicker_option">
																	<option value="1" <?php echo ( ($flagInfoEnpal == 1 AND $infoEnpalPCA["pca_recommendation"] == 1) ? 'selected' : ''); ?>><?php echo $txtArray["Zaposliti"][$languageUser]; ?></option>
																	<option value="2" <?php echo ( ($flagInfoEnpal == 1 AND $infoEnpalPCA["pca_recommendation"] == 2) ? 'selected' : ''); ?>><?php echo $txtArray["Odbiti"][$languageUser]; ?></option>
																	<option value="3" <?php echo ( ($flagInfoEnpal == 1 AND $infoEnpalPCA["pca_recommendation"] == 3) ? 'selected' : ''); ?>><?php echo $txtArray["Neodlučno"][$languageUser]; ?></option>
																</select>
															</div>
														</div>
													</div>
												</div>
												<div class = "row pt-3">
													<div class = "col-12">
														<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Glavni razlog za (odluku o zapošljavanju) / konačna presuda"][$languageUser]; ?></p>
													</div>
												</div>
												<div class = "row pt-3">
													<div class = "col-lg-12">
														<div class = "row ">
															<div class = "col-12">
																<textarea class="textArreaStyle" rows="2" disabled><?php echo ( ($flagInfoEnpal == 1 AND $infoEnpalPCA["pca_reason_recommendation"] != '') ? $infoEnpalPCA["pca_reason_recommendation"] : null  );?></textarea>
															</div>
														</div>
													</div>
												</div>
												<div class = "row pt-3">
													<div class = "col-lg-12">
														<script>
															$(document).ready(function(){
																var candidateIdVar = parseInt("<?php echo $kandidat_id; ?>");
																var pcaIdVar = parseInt("<?php echo $pca_id; ?>");
																//$("#avgRating").html("");
																$.ajax({
																	url: 'ajax.php?action=candidateAvgRatingEnpal',
																	type: 'POST',
																	data: {
																		'candidateIdVar':candidateIdVar,
																		'pcaIdVar':pcaIdVar,
																	},
																	dataType: 'html',
																	success: function(data){
																		if(data != ""){
																		$("#avgRating").html(data);
																		$("#avgRating").show( 'slide', 100);
																		}
																	},
																	error: function (xhr, ajaxOptions, thrownError) {
																		alert(xhr.status);
																		alert(thrownError);
																	}
																});
															});
														</script>
														<div id = "avgRating" style = "display: none;">
															
														</div>
													</div>
												</div>
											<!--
											General info END
										-->
									</div>
									<!--
											SCRIPT General info START
										-->
											<script>
												$(document).ready(function() {
													$('.selectpicker_option').selectpicker();
													$('.selectpicker_option').prop('disabled', true);
													$('.selectpicker_option').selectpicker('refresh');
												});
											</script>
										<!--
											SCRIPT General info END
									-->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	<?php 
	break; 
	
	case "candidateViewInterview":
		$kandidat_id 	= $_POST["kandidat_id"];
		$pap_id 		= $_POST["pap_id"];
		$pca_id 		= $_POST["pca_id"];
		$type 			= $_POST["type"];
		$bar_id 		= $_POST["bar_id"];
		$nalog_id 		= $_POST["nalog_id"];
		$candidate_key 	= getCandidateKeyById($kandidat_id);
	?>
			<div class = "row">
				<div class = "col-12">
					<div class = "row align-items-center">
						<div class = "col-1 text-center goToProfile1 cursorPointer">
							<i class="fa fa-angle-left fa-3x" aria-hidden="true"></i>
						</div>
						<div class = "col-11 text-center">
							<p class = "text_color mb-0" style = "font-style: normal; font-weight: bold; font-size: 28px;"><?php echo $txtArray["Detalji o intervjuu"][$languageUser]; ?></p>
						</div>
					</div>
					<script>
						$('#candidateViewInterview .goToProfile1').on('click', function() {
							getLoaderBig();
							var readyKanId = "<?php echo $candidate_key; ?>";
							var type = parseInt("<?php echo $type; ?>");
							var bar_id = parseInt("<?php echo $bar_id; ?>");
							var nalog_id = parseInt("<?php echo $nalog_id; ?>");
							$.ajax({
								url: 'ajax.php?action=candidateProfile',
								type: 'POST',
								data: {
									'kandidat_id':readyKanId,
									'type':type,
									'bar_id':bar_id,
									'nalog_id':nalog_id
								},
								dataType: 'html',
								success: function(data){
									$("#candidateProfile").html(data);
									$( "#candidateViewInterview" ).hide( 'slide', 500, function(){
										$( "#candidateViewInterview" ).html( '' );
										$( "#candidateProfile" ).show( 'slide', 1000);
										$("#edit_candidate_partner_data").unbind('click').bind('click', handleEditCandidatePartnerData);
									});
									removeLoader();
								},
								error: function (xhr, ajaxOptions, thrownError) {
									alert(xhr.status);
									alert(thrownError);
								}
							});
						});
					</script>
					<div class = "row gx-5 gy-3">
						<div class = "col-md-12 ">
							<div class = "row">
								<div class = "col-12">
									<div class = "px-5" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
										<div class = "row">
											<div class = "col-12">
												<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Opšti komentar"][$languageUser]; ?></p>
											</div>
										</div>
										<div class = "row pt-3">
											<div class = "col-lg-12">
												<div class = "row ">
													<div class = "col-12">
														<?php 
															//Provjera da li je vec odgovoreno
															$commentGeneral = getCommentPCA($kandidat_id, $pca_id);
															if($commentGeneral != 101 AND $commentGeneral != 102){
																$commentGeneral = $commentGeneral;
															}else{
																$commentGeneral = "";
															}
														?>
														<textarea class="textArreaStyle" rows="2" disabled><?php echo $commentGeneral;?></textarea>
													</div>
												</div>
											</div>
										</div>
										<!--
											Pitanja i ocjenjivanje START
											-->
											<div class = "row pt-5">
													<div class = "col-12">
														<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Pitanja i ocjenjivanje"][$languageUser]; ?></p>
													</div>
												</div>
												<div class = "row pt-3">
													<div class = "col-12">
														<!-- 
															Pitanja Start
															-->
																<?php 
																	$appQuestions = array();
																	$appQuestions =  getAppointmentQuestionForAppointmentArrayR($pap_id);

																	if(count($appQuestions["count"]) != 0){
																		foreach($appQuestions["count"] AS $countAppQuestions){
																			?>

																				<!--
																					Jedna grupa Start 
																					-->
																						<?php 
																							if ($appQuestions["categoryStart"][$countAppQuestions] == 1 AND $appQuestions["categoryId"][$countAppQuestions] != 0) {
																								?>
																									<hr class="border border-secondary border-2 opacity-50 mb-2 <?php echo ($countAppQuestions != 0) ? "mt-5" : "mt-2";?>">
																									<div class = "row px-3 py-3 mb-3">
																										<div class = "col-lg-12">
																											<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 19px;"><?php echo $appQuestions["categoryName"][$countAppQuestions];?></p>
																										</div>
																									</div>
																								<?php 
																							}
																						?>
																					<!--
																					Jedna grupa END 
																				-->

																				<!--
																					Jedno pitanje Start 
																					-->
																						<div class = "row px-3 py-3 mb-3 rounded shadow-sm">
																							<div class = "col-12">
																								<?php 
																									$classText = "";
																									$classRating = "";
																									$classDropdown = "";

																									if ($appQuestions["pqu_has_rating"][$countAppQuestions] == 1 AND $appQuestions["pqu_has_dropdown"][$countAppQuestions] == 1 ) {
																										$classText = "col-lg-6";
																										$classRating = "col-lg-3";
																										$classDropdown = "col-lg-3";
																									} else if ($appQuestions["pqu_has_rating"][$countAppQuestions] == 1 AND $appQuestions["pqu_has_dropdown"][$countAppQuestions] == 0) {
																										$classText = "col-lg-7";
																										$classRating = "col-lg-5";
																										$classDropdown = "col-lg";
																									} else if ($appQuestions["pqu_has_rating"][$countAppQuestions] == 0 AND $appQuestions["pqu_has_dropdown"][$countAppQuestions] == 1) {
																										$classText = "col-lg-7";
																										$classRating = "col-lg";
																										$classDropdown = "col-lg-5";
																									} else {
																										$classText = "col-lg-12";
																										$classRating = "col-lg";
																										$classDropdown = "col-lg";
																									}
																								?>
																								<div class = "row align-items-center my-2">
																									<!--
																										Text Pitanja Start
																										-->
																											<div class = "<?php echo $classText; ?>">
																												<div class = "row align-items-center">
																													<!--<div class = "col-12">
																														<p class = "mb-0" style = "color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php //echo $txtArray["Pitanje"][$languageUser]; ?> #<?php //echo $countAppQuestions + 1;?></p>
																													</div>-->
																													<div class = "col-12">
																														<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;"><?php echo $appQuestions["pqu_question"][$countAppQuestions];?></p>
																													</div>
																												</div>
																											</div>
																										<!--
																										Text Pitanja END
																									-->
																									<?php 
																										if($appQuestions["pqu_has_rating"][$countAppQuestions] == 1) {
																											?>
																												<!--
																													Ocjena Pitanja Start
																													-->
																														<div class = "<?php echo $classRating; ?>">
																															<div class = "row align-items-center">
																																<div class = "col-12 text-center">
																																	<?php 
																																		//Provjera da li je vec odgovoreno
																																		$ratingForQuestion = getRatingForAppointmentQuestion($kandidat_id, $appQuestions["papq_id"][$countAppQuestions]);
																																		$arrayCheckedValue = array(
																																			"1" => array("fa fa-star-o"),
																																			"2" => array("fa fa-star-o"),
																																			"3" => array("fa fa-star-o"),
																																			"4" => array("fa fa-star-o"),
																																			"5" => array("fa fa-star-o")
																																		);
																																		if($ratingForQuestion != 100 AND $ratingForQuestion != 101 AND $ratingForQuestion != 102){
																																			for($i = 1; $i <= $ratingForQuestion; $i++){
																																				$arrayCheckedValue[$i][0] = "fa fa-star";
																																			}
																																		}else{
																																			$arrayCheckedValue["1"][0] = "fa fa-star-o";
																																		}
																																	?>
																																	<ul class=" list-group list-group-horizontal justify-content-center">
																																		<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["1"][0]; ?> fa-2x" style = "color: #FDD878;" aria-hidden="true"></i></li>
																																		<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["2"][0]; ?> fa-2x" style = "color: #FDD878;" aria-hidden="true"></i></li>
																																		<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["3"][0]; ?> fa-2x" style = "color: #FDD878;" aria-hidden="true"></i></li>
																																		<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["4"][0]; ?> fa-2x" style = "color: #FDD878;" aria-hidden="true"></i></li>
																																		<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["5"][0]; ?> fa-2x" style = "color: #FDD878;" aria-hidden="true"></i></li>
																																	</ul>
																																	<?php 
																																		unset($arrayCheckedValue);
																																	?>
																																</div>
																															</div>
																														</div>
																													<!--
																													Ocjena Pitanja END
																												-->
																											<?php
																										}
																										
																										if($appQuestions["pqu_has_dropdown"][$countAppQuestions] == 1) {

																											$questionOptions = getQuestionOptionsArrayR($appQuestions["pqu_id"][$countAppQuestions]);
																											$valueOptionForQuestion = getOptionForAppointmentQuestion($kandidat_id, $appQuestions["papq_id"][$countAppQuestions]);

																											?>
																												<!--
																													Dropdown Pitanja Start
																													-->
																														<div class = "<?php echo $classDropdown; ?>">
																															<div class = "row align-items-center">
																																<div class = "col-12">
																																	<select class="selectpicker col-12 selectpicker_option" title="<?php echo $txtArray['Odaberite opciju'][$languageUser]; ?>">
																																		<?php 
																																			if (count($questionOptions["count"]) != 0) {

																																				foreach($questionOptions["count"] AS $countQuestionOptions) {

																																					?>

																																						<option 
																																							value="<?php echo $questionOptions["pqo_value"][$countQuestionOptions]; ?>" 
																																							<?php echo ( ($questionOptions["pqo_value"][$countQuestionOptions] == $valueOptionForQuestion ) ? 'selected' : ''); ?>
																																							<?php echo ( ($questionOptions["pqo_value_subtext"][$countQuestionOptions] != NULL) ? 'data-subtext="'.$questionOptions["pqo_value_subtext"][$countQuestionOptions].'"' : '' ); ?>
																																						>
																																							<?php echo $questionOptions["pqo_value_text"][$countQuestionOptions]; ?>
																																						</option>
																																					
																																					<?php

																																				}

																																			}
																																		?>
																																	</select>
																																</div>
																															</div>
																														</div>
																													<!--
																													Dropdown Pitanja END
																												-->
																											<?php 
																										}
																									?>
																								</div>
																								<?php 
																									if($appQuestions["pqu_has_text"][$countAppQuestions] == 1) {
																										?>
																											<!--
																												Biljeska Pitanja Start
																												-->
																													<div class = "row align-items-center">
																														<div class = "col-lg-12">
																															<div class = "row align-items-center">
																																<div class = "col-12">
																																	<?php 
																																		//Provjera da li je vec odgovoreno
																																		$commentForQuestion = getCommentForAppointmentQuestion($kandidat_id, $appQuestions["papq_id"][$countAppQuestions]);
																																		if($commentForQuestion != 100 AND $commentForQuestion != 101 AND $commentForQuestion != 102){
																																			$commentForQuestion = $commentForQuestion;
																																		}else{
																																			$commentForQuestion = "";
																																		}
																																	?>
																																	<textarea class="textArreaStyle" rows="2" disabled><?php echo $commentForQuestion;?></textarea>
																																</div>
																															</div>
																														</div>
																													</div>
																												<!--
																												Biljeska Pitanja END
																											-->
																										<?php 
																									}
																								?>
																							</div>
																						</div>
																					<!--
																					Jedno pitanje END 
																				-->

																			<?php 
																		}
																	}

																	unset($appQuestions);
																?>
															<!-- 
															Pitanja End
														-->
													</div>
												</div>
											<!--
											Pitanja i ocjenjivanje END
										-->
										<div class = "row pt-2">
											<div class = "col-12">
												<script>
													$(document).ready(function(){
														var kanIdAvgRat = parseInt("<?php echo $kandidat_id; ?>");
														var pca_id = parseInt("<?php echo $pca_id; ?>");
														//$("#avgRating").html("");
														$.ajax({
															url: 'ajax.php?action=candidateAvgRating',
															type: 'POST',
															data: {
																'kanIdAvgRat':kanIdAvgRat,
																'pca_id':pca_id,
															},
															dataType: 'html',
															success: function(data){
																if(data != ""){
																$("#avgRating").html(data);
																$( "#avgRating" ).show( 'slide', 10);
																}
															},
															error: function (xhr, ajaxOptions, thrownError) {
																alert(xhr.status);
																alert(thrownError);
															}
														});
														// $( "#candidateInter" ).hide(function(){
															// $( "#candidateProfile" ).show( 'slide', 500);
														// });
													});
												</script>
												<div id = "avgRating" style = "display: none;">
													
												</div>
											</div>
										</div>
										<script>
											$(document).ready(function() {
												$('.selectpicker_option').selectpicker();
												$('.selectpicker_option').prop('disabled', true);
												$('.selectpicker_option').selectpicker('refresh');
											});
										</script>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
					
	<?php
	break;
	
	case "candidateInsertInterviewEnpal": 
		$kandidat_id 	= $_POST["kandidat_id"];
		$pap_id 		= $_POST["pap_id"];
		$pca_id 		= $_POST["pca_id"];
		$type 			= $_POST["type"];
		$bar_id 		= $_POST["bar_id"];
		$nalog_id 		= $_POST["nalog_id"];
		$candidate_key 	= getCandidateKeyById($kandidat_id);
	?>
		<div class = "row">
			<div class = "col-12">
				<div class = "row align-items-center">
					<div class = "col-1 text-center goToProfile2 cursorPointer">
						<i class="fa fa-angle-left fa-3x" aria-hidden="true"></i>
					</div>
					<div class = "col-11 text-center">
						<p class = "text_color mb-0" style = "font-style: normal; font-weight: bold; font-size: 28px;"><?php echo $txtArray["Ocjenite intervju"][$languageUser]; ?></p>
						
					</div>
				</div>
				<script>
					$('.goToProfile2').on('click', function() {
						getLoaderBig();
						var readyKanId = "<?php echo $candidate_key; ?>";
						var type = parseInt("<?php echo $type; ?>");
						var bar_id = parseInt("<?php echo $bar_id; ?>");
						var nalog_id = parseInt("<?php echo $nalog_id; ?>");
						$.ajax({
							url: 'ajax.php?action=candidateProfile',
							type: 'POST',
							data: {
								'kandidat_id':readyKanId,
								'type':type,
								'bar_id':bar_id,
								'nalog_id':nalog_id
							},
							dataType: 'html',
							success: function(data){
								$("#candidateProfile").html(data);
								$( "#candidateInsertInterview" ).hide( 'slide', 250, function(){
									$( "#candidateInsertInterview" ).html( '' );
									$( "#candidateProfile" ).show( 'slide', 250);
									$("#edit_candidate_partner_data").unbind('click').bind('click', handleEditCandidatePartnerData);
								});
								removeLoader();
							},
							error: function (xhr, ajaxOptions, thrownError) {
								alert(xhr.status);
								alert(thrownError);
							}
						});
					});
				</script>
				<div class = "row gx-5 gy-3">
					<div class = "col-md-12 ">
						<div class = "row">
							<div class = "col-12">
								<div class = "px-5" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
									<script>
										function getCandidateAvgRatingEnpal(candidateIdVar, candidateKeyVar, candidateNalogVar, pcaIdVar){
											$.ajax({
												url: 'ajax.php?action=candidateAvgRatingEnpal',  
												type: 'POST',
												data: {
													'candidateIdVar':candidateIdVar,
													'pcaIdVar':pcaIdVar,
												},
												dataType: 'html',
												success: function(data){
													if(data != ""){
														getAccessControl(candidateNalogVar, candidateKeyVar, null, 1, function(response_access_control){
															var reminder_status 			= response_access_control['status'];
															var reminder_id 				= response_access_control['id'];
															var reminder_assigned_user 		= response_access_control['assigned'];
															var reminder_is_logged_user 	= response_access_control['isLogged'];
															var reminder_response_message	= response_access_control['message'];
															var reminder_response_title		= response_access_control['title'];

															if (reminder_status == 103 || reminder_status == 1 || reminder_status == 2) {
																updateReminderStatus(reminder_id, 3, function(response_update_reminder){
																	var update_status 	= response_update_reminder["status"];
																	var update_message 	= response_update_reminder["message"];
																	if(update_status == 3){
																		showToast(update_message, reminder_response_title);
																	}
																});
															}
														});
														
														$("#avgRating").html(data);
														$("#avgRating").show( 'slide', 100);
													}
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
										}
									</script>
									<!--
										General info START
										-->
											<?php 
												//Provjera da li je vec odgovoreno
												$flagInfoEnpal = 0;
												$infoEnpalPCA = getInfoEnpalPCAArrayR($kandidat_id, $pca_id);
												if($infoEnpalPCA["status"] == 1){
													$flagInfoEnpal = 1;
												}
											?>

											<div class = "row">
												<div class = "col-12">
													<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Intervjuer"][$languageUser]; ?></p>
												</div>
											</div>
											<div class = "row pt-3">
												<div class = "col-lg-12">
													<div class = "row ">
														<div class = "col-12">
															<textarea class="textArreaStyle saveGeneralInfo" id="interviewerGeneral" rows="1" placeholder="John Jones, Angela Owens..." data-field_general="pca_interviewer" data-pca_id = "<?php echo $pca_id;?>"><?php echo ( ($flagInfoEnpal == 1 AND $infoEnpalPCA["pca_interviewer"] != '') ? $infoEnpalPCA["pca_interviewer"] : null  );?></textarea>
														</div>
													</div>
												</div>
											</div>
										<!--
										General info END
									-->
									<!--
										Pitanja i ocjenjivanje START
										-->
											<div class = "row pt-5">
												<div class = "col-12">
													<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Pitanja i ocjenjivanje"][$languageUser]; ?></p>
												</div>
											</div>
											<div class = "row pt-3">
												<div class = "col-12">
													<!-- 
														Pitanja Start
														-->
															<?php 
																$appQuestions = array();
																$appQuestions =  getAppointmentQuestionForAppointmentEnpalArrayR($pap_id);

																if(count($appQuestions["count"]) != 0){
																	foreach($appQuestions["count"] AS $countAppQuestions){
																		?>

																			<!--
																				Jedna grupa Start 
																				-->
																					<?php 
																						if ($appQuestions["categoryStart"][$countAppQuestions] == 1 AND $appQuestions["categoryId"][$countAppQuestions] != 0) {
																							?>
																								<hr class="border border-secondary border-2 opacity-50 mb-2 <?php echo ($countAppQuestions != 0) ? "mt-5" : "mt-2";?>">
																								<div class = "row px-3 py-3 mb-3">
																									<div class = "col-lg-12">
																										<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 19px;"><?php echo $appQuestions["categoryName"][$countAppQuestions];?></p>
																									</div>
																								</div>
																							<?php 
																						}
																					?>
																				<!--
																				Jedna grupa END 
																			-->

																			<!--
																				Jedno pitanje Start 
																				-->
																					<div class = "row px-3 py-3 mb-3 rounded shadow-sm">
																						<div class = "col-12">
																							<?php 
																								$classText = "";
																								$classRating = "";
																								$classDropdown = "";

																								if ($appQuestions["pqu_has_rating"][$countAppQuestions] == 1 AND $appQuestions["pqu_has_dropdown"][$countAppQuestions] == 1 ) {
																									$classText = "col-lg-6";
																									$classRating = "col-lg-3";
																									$classDropdown = "col-lg-3";
																								} else if ($appQuestions["pqu_has_rating"][$countAppQuestions] == 1 AND $appQuestions["pqu_has_dropdown"][$countAppQuestions] == 0) {
																									$classText = "col-lg-7";
																									$classRating = "col-lg-5";
																									$classDropdown = "col-lg";
																								} else if ($appQuestions["pqu_has_rating"][$countAppQuestions] == 0 AND $appQuestions["pqu_has_dropdown"][$countAppQuestions] == 1) {
																									$classText = "col-lg-7";
																									$classRating = "col-lg";
																									$classDropdown = "col-lg-5";
																								} else {
																									$classText = "col-lg-12";
																									$classRating = "col-lg";
																									$classDropdown = "col-lg";
																								}
																							?>
																							<div class = "row align-items-center my-2">
																								<!--
																									Text Pitanja Start
																									-->
																										<div class = "<?php echo $classText; ?>">
																											<div class = "row align-items-center">
																												<!--<div class = "col-12">
																													<p class = "mb-0" style = "color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php //echo $txtArray["Pitanje"][$languageUser]; ?> #<?php //echo $countAppQuestions + 1;?></p>
																												</div>-->
																												<div class = "col-12">
																													<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;"><?php echo $appQuestions["pqu_question"][$countAppQuestions];?></p>
																												</div>
																											</div>
																										</div>
																									<!--
																									Text Pitanja END
																								-->
																								<?php 
																									if($appQuestions["pqu_has_rating"][$countAppQuestions] == 1) {
																										?>
																											<!--
																												Ocjena Pitanja Start
																												-->
																													<div class = "<?php echo $classRating; ?>">
																														<div class = "row align-items-center">
																															<div class = "col-12 text-center">
																																<?php 
																																	//Provjera da li je vec odgovoreno
																																	$ratingForQuestion = getRatingForAppointmentQuestion($kandidat_id, $appQuestions["papq_id"][$countAppQuestions]);
																																	$arrayCheckedValue = array(
																																		"1" => array(""),
																																		"2" => array(""),
																																		"3" => array(""),
																																		"4" => array(""),
																																		"5" => array("")
																																	);
																																	if($ratingForQuestion != 100 AND $ratingForQuestion != 101 AND $ratingForQuestion != 102){
																																		$arrayCheckedValue[$ratingForQuestion][0] = "checked";
																																	}else{
																																		$arrayCheckedValue["1"][0] = "";
																																	}
																																?>
																																<style>
																																	/* 
																																		Ocjenjivanje style START
																																		*/
																																			/* Ovo je dinamicni CSS - svaka iteracija svoj CSS */
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> {direction: rtl; margin: 0 auto;}
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> label {cursor: pointer; padding: 2px; margin: 0;}
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> .fa {color: #FDD878;}
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> input:not(:checked) ~ label:not([for=""]) > .fa-star {display: none;}
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> label:hover > .fa-star-o,
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> label:hover ~ label > .fa-star-o,
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> input:checked ~ label > .fa-star-o {display: none;}
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> label:hover > .fa-star,
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> label:hover ~ label > .fa-star,
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> input:checked ~ label > .fa-star {display: inline-block !important;} 
																																		/* 
																																		Ocjenjivanje style END
																																	*/ 
																																</style>
																																<div class="stars<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																	<input type="radio" name="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>" id="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-4" hidden <?php echo $arrayCheckedValue["4"][0]; ?>>
																																	<label for="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-4" class = "ratingInsertValue" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-value="4" data-column_name="pra_rating" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																		<i class="fa fa-star fa-2x"></i>
																																		<i class="fa fa-star-o fa-2x"></i>
																																	</label>
																																	<input type="radio" name="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>" id="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-3" hidden <?php echo $arrayCheckedValue["3"][0]; ?>>
																																	<label for="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-3" class = "ratingInsertValue" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-value="3" data-column_name="pra_rating" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																		<i class="fa fa-star fa-2x"></i>
																																		<i class="fa fa-star-o fa-2x"></i>
																																	</label>
																																	<input type="radio" name="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>" id="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-2" hidden <?php echo $arrayCheckedValue["2"][0]; ?>>
																																	<label for="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-2" class = "ratingInsertValue" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-value="2" data-column_name="pra_rating" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																		<i class="fa fa-star fa-2x"></i>
																																		<i class="fa fa-star-o fa-2x"></i>
																																	</label>
																																	<input type="radio" name="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>" id="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-1" hidden <?php echo $arrayCheckedValue["1"][0]; ?>>
																																	<label for="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-1" class = "ratingInsertValue" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-value="1" data-column_name="pra_rating" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																		<i class="fa fa-star fa-2x"></i>
																																		<i class="fa fa-star-o fa-2x"></i>
																																	</label>
																																</div>
																																<?php 
																																	unset($arrayCheckedValue);
																																?>
																															</div>
																														</div>
																													</div>
																												<!--
																												Ocjena Pitanja END
																											-->
																										<?php
																									}
																									
																									if($appQuestions["pqu_has_dropdown"][$countAppQuestions] == 1) {

																										$questionOptions = getQuestionOptionsArrayR($appQuestions["pqu_id"][$countAppQuestions]);
																										$valueOptionForQuestion = getOptionForAppointmentQuestion($kandidat_id, $appQuestions["papq_id"][$countAppQuestions]);

																										?>
																											<!--
																												Dropdown Pitanja Start
																												-->
																													<div class = "<?php echo $classDropdown; ?>">
																														<div class = "row align-items-center">
																															<div class = "col-12">
																																<select class="selectpicker col-12 selectpicker_option optionInsertValue" title="<?php echo $txtArray['Odaberite opciju'][$languageUser]; ?>" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-column_name="pra_options_value" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																	<?php 
																																		if (count($questionOptions["count"]) != 0) {

																																			foreach($questionOptions["count"] AS $countQuestionOptions) {

																																				?>

																																					<option 
																																						value="<?php echo $questionOptions["pqo_value"][$countQuestionOptions]; ?>" 
																																						<?php echo ( ($questionOptions["pqo_value"][$countQuestionOptions] == $valueOptionForQuestion ) ? 'selected' : ''); ?>
																																						<?php echo ( ($questionOptions["pqo_value_subtext"][$countQuestionOptions] != NULL) ? 'data-subtext="'.$questionOptions["pqo_value_subtext"][$countQuestionOptions].'"' : '' ); ?>
																																					>
																																						<?php echo $questionOptions["pqo_value_text"][$countQuestionOptions]; ?>
																																					</option>
																																				
																																				<?php

																																			}

																																		}
																																	?>
																																</select>
																															</div>
																														</div>
																													</div>
																												<!--
																												Dropdown Pitanja END
																											-->
																										<?php 
																									}
																								?>
																							</div>
																							<?php 
																								if($appQuestions["pqu_has_text"][$countAppQuestions] == 1) {
																									?>
																										<!--
																											Biljeska Pitanja Start
																											-->
																												<div class = "row align-items-center">
																													<div class = "col-lg-12">
																														<div class = "row align-items-center">
																															<div class = "col-12">
																																<?php 
																																	//Provjera da li je vec odgovoreno
																																	$commentForQuestion = getCommentForAppointmentQuestion($kandidat_id, $appQuestions["papq_id"][$countAppQuestions]);
																																	if($commentForQuestion != 100 AND $commentForQuestion != 101 AND $commentForQuestion != 102){
																																		$commentsDefaultRows = 2;
																																		$commentForQuestion = $commentForQuestion;
																																		if ($appQuestions["pqu_question"][$countAppQuestions] == "1 Phase - Relaxing into the interview") {
																																			$commentsDefaultRows = 2;
																																		} else if ($appQuestions["pqu_question"][$countAppQuestions] == "2 Phase - Job Experience and Motive") {
																																			$commentsDefaultRows = 8;
																																		} else if ($appQuestions["pqu_question"][$countAppQuestions] == "3 Phase - Anti selling") {
																																			$commentsDefaultRows = 6;
																																		} else if($appQuestions["pqu_question"][$countAppQuestions] == "First Impression, Communication and Attitude") {
																																			$commentsDefaultRows = 6;
																																		} else if($appQuestions["pqu_question"][$countAppQuestions] == "Tools and Practical Test Assessment") {
																																			$commentsDefaultRows = 8;
																																		} else if($appQuestions["pqu_question"][$countAppQuestions] == "Relevant Experience and Learning Capabilities") {
																																			$commentsDefaultRows = 9;
																																		} else if($appQuestions["pqu_question"][$countAppQuestions] == "Longterm Commitment and Motivation for Hard Work") {
																																			$commentsDefaultRows = 10;
																																		} else {
																																			$commentsDefaultRows = 2;
																																		}
																																	}else{
																																		$commentsDefaultRows = 2;
																																		$commentForQuestion = "";
																																		if ($appQuestions["pqu_question"][$countAppQuestions] == "1 Phase - Relaxing into the interview") {
																																			$commentsDefaultRows = 2;
																																			$commentForQuestion = "General Impression:\n\n";
																																		} else if ($appQuestions["pqu_question"][$countAppQuestions] == "2 Phase - Job Experience and Motive") {
																																			$commentsDefaultRows = 8;
																																			$commentForQuestion = "Job Experience?\n\nWhy this Job?\n\nExperience in Germany?\n\nFamily Stance?\n\n";
																																		} else if ($appQuestions["pqu_question"][$countAppQuestions] == "3 Phase - Anti selling") {
																																			$commentsDefaultRows = 6;
																																			$commentForQuestion = "Reaction to Hard Anti-selling\n\nWorked in these conditions before?\n\nHard working / Works over hours?\n\n";
																																		} else if($appQuestions["pqu_question"][$countAppQuestions] == "First Impression, Communication and Attitude") {
																																			$commentsDefaultRows = 6;
																																			$commentForQuestion = "How did he greet you? \n\nHow does he speak? (about himself, his jobs, his family,…) \n\nHow does he appear? (well-dressed, tired, genuinely interested?,…) ";
																																		} else if($appQuestions["pqu_question"][$countAppQuestions] == "Tools and Practical Test Assessment") {
																																			$commentsDefaultRows = 8;
																																			$commentForQuestion = "Which practical test did you like better and why? \n\nHave you worked with the tools before? \n\nCan you name the tools you used? \n\nWhich size did the dowel and cable have? ";
																																		} else if($appQuestions["pqu_question"][$countAppQuestions] == "Relevant Experience and Learning Capabilities") {
																																			$commentsDefaultRows = 9;
																																			$commentForQuestion = "What is your most recent electrician experience? \n\nWhy should Enpal invest so much money (for language school, academy and transfer to Germany) in you and not another candidate? \n\nCan you give an example from your previous experiences where you learned sth more quickly than others/showed you work harder than others/solved a task more quickly than your team lead assumed/… \n\nHave you worked outside of Bosnia/Serbia already? ";
																																		} else if($appQuestions["pqu_question"][$countAppQuestions] == "Longterm Commitment and Motivation for Hard Work") {
																																			$commentsDefaultRows = 10;
																																			$commentForQuestion = "Why do you want to start working for Enpal in Germany? \n\nHow does your family think about you wanting to move to Germany? Are you planning to reunite with them in Germany? \n\nWhere do you see yourself in five years? \n\n How does a typical day look like in your current/previous job? When do you start, when do you finish? \n\nHow often do you have to work overtime? ";
																																		} else {
																																			$commentsDefaultRows = 2;
																																			$commentForQuestion = "";
																																		}
																																	}
																																?>
																																<textarea class="textArreaStyle commentInsertValue" rows="<?php echo $commentsDefaultRows; ?>" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-column_name="pra_comment" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>"><?php echo $commentForQuestion;?></textarea>
																															</div>
																														</div>
																													</div>
																												</div>
																											<!--
																											Biljeska Pitanja END
																										-->
																									<?php 
																								}
																							?>
																						</div>
																					</div>
																				<!--
																				Jedno pitanje END 
																			-->

																		<?php 
																	}
																}

																unset($appQuestions);
															?>
														<!-- 
														Pitanja End
													-->

													<!-- 
														SCRIPT PITANJA START 
														-->
														
															<script>
																function valueInsertUpdateForCandidateQuestion(candidateId, candidateKey, candidateNalog, papqId, pcaId, columnName, value){

																	var candidateIdVar = parseInt(candidateId);
																	var candidateKeyVar = candidateKey;
																	var candidateNalogVar = parseInt(candidateNalog);
																	var papqIdVar = parseInt(papqId);
																	var pcaIdVar = parseInt(pcaId);
																	var columnNameVar = columnName;

																	if (columnNameVar === "pra_rating") {
																		var valueVar = parseInt(value);
																	} else if (columnNameVar === "pra_options_value") {
																		var valueVar = parseInt(value);
																	} else {
																		var valueVar = value;
																	}

																	//alert(" candidateIdVar:"+candidateIdVar+" candidateKeyVar:"+candidateKeyVar+" candidateNalogVar:"+candidateNalogVar+" papqIdVar:"+papqIdVar+" pcaIdVar:"+pcaIdVar+" columnNameVar:"+columnNameVar+" valueVar:"+valueVar);
																	/*
																		backend request START
																		*/
																			$.ajax({
																				url: 'ajax.php?action=insert_update_candidate_question',
																				type: 'POST',
																				data: {
																					'candidateIdVar': candidateIdVar,
																					'candidateNalogVar': candidateNalogVar,
																					'papqIdVar': papqIdVar,
																					'pcaIdVar': pcaIdVar,
																					'columnNameVar': columnNameVar,
																					'valueVar': valueVar
																				},
																				dataType: 'html',
																				success: function(data){
																					/*
																						AVG Rating START
																						*/
																							const obj = JSON.parse(data);

																							if (obj.status !== 102) {
																								if (obj.avgUpdate == 1) {
																									
																									getCandidateAvgRatingEnpal(candidateIdVar, candidateKeyVar, candidateNalogVar, pcaIdVar);

																								} 
																							}

																						/*
																						AVG Rating END
																					*/
																				},
																				error: function (xhr, ajaxOptions, thrownError) {
																					alert(xhr.status);
																					alert(thrownError);
																				}
																			});
																		/*
																		backend request END
																	*/

																}

																$(".ratingInsertValue").on("click",function() {

																	var candidate_id = $(this).data("candidate_id");
																	var candidate_key = $(this).data("candidate_key");
																	var nalog_id = $(this).data("nalog_id");
																	var papq_id = $(this).data("papq_id");
																	var pca_id = $(this).data("pca_id");
																	var column_name = $(this).data("column_name");
																	var value = $(this).data("value");

																	valueInsertUpdateForCandidateQuestion(candidate_id, candidate_key, nalog_id, papq_id, pca_id, column_name, value); 

																});

																var timeoutIdComment;
																$('.commentInsertValue').on('input propertychange', function() {
																	var candidate_id = $(this).data("candidate_id");
																	var candidate_key = $(this).data("candidate_key");
																	var nalog_id = $(this).data("nalog_id");
																	var papq_id = $(this).data("papq_id");
																	var pca_id = $(this).data("pca_id");
																	var column_name = $(this).data("column_name");
																	var value = $(this).val();

																	clearTimeout(timeoutIdComment);
																	timeoutIdComment = setTimeout(function() {
																		if(value == ""){
																			$("#comment"+papq_id+"").effect('shake' ,500);
																		}else{
																			valueInsertUpdateForCandidateQuestion(candidate_id, candidate_key, nalog_id, papq_id, pca_id, column_name, value); 
																		}
																	}, 1000);

																});

																$(".optionInsertValue").on("change",function() {

																	var candidate_id = $(this).data("candidate_id");
																	var candidate_key = $(this).data("candidate_key");
																	var nalog_id = $(this).data("nalog_id");
																	var papq_id = $(this).data("papq_id");
																	var pca_id = $(this).data("pca_id");
																	var column_name = $(this).data("column_name");
																	var value = $(this).val();

																	valueInsertUpdateForCandidateQuestion(candidate_id, candidate_key, nalog_id, papq_id, pca_id, column_name, value); 

																});

															</script>
														<!--
															SCRIPT PITANJA START
													-->
												</div>
											</div>
										<!--
										Pitanja i ocjenjivanje END
									-->
									<!--
										General info START
										-->
											<div class = "row pt-3" style="display: none;">
												<div class = "col-12">
													<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Opšti komentar"][$languageUser]; ?></p>
												</div>
											</div>
											<div class = "row pt-3" style="display: none;">
												<div class = "col-lg-12">
													<div class = "row ">
														<div class = "col-12">
															<textarea class="textArreaStyle saveGeneralInfo" id="commentGeneral" rows="2" data-field_general="pca_comment" data-pca_id = "<?php echo $pca_id;?>"><?php echo ( ($flagInfoEnpal == 1 AND $infoEnpalPCA["pca_comment"] != '') ? $infoEnpalPCA["pca_comment"] : null  );?></textarea>
														</div>
													</div>
												</div>
											</div>
											<div class = "row pt-3">
												<div class = "col-12">
													<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Preporuka"][$languageUser]; ?></p>
												</div>
											</div>
											<div class = "row pt-3">
												<div class = "col-lg-12">
													<div class = "row ">
														<div class = "col-12">
															<select class="selectpicker col-12 selectpicker_option saveGeneralRecommentation" id="recommendationGeneral" data-field_general="pca_recommendation" data-pca_id = "<?php echo $pca_id;?>" title="<?php echo $txtArray['Odaberite opciju'][$languageUser]; ?>">
																<option value="1" <?php echo ( ($flagInfoEnpal == 1 AND $infoEnpalPCA["pca_recommendation"] == 1) ? 'selected' : ''); ?>><?php echo $txtArray["Zaposliti"][$languageUser]; ?></option>
																<option value="2" <?php echo ( ($flagInfoEnpal == 1 AND $infoEnpalPCA["pca_recommendation"] == 2) ? 'selected' : ''); ?>><?php echo $txtArray["Odbiti"][$languageUser]; ?></option>
																<option value="3" <?php echo ( ($flagInfoEnpal == 1 AND $infoEnpalPCA["pca_recommendation"] == 3) ? 'selected' : ''); ?>><?php echo $txtArray["Neodlučno"][$languageUser]; ?></option>
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class = "row pt-3">
												<div class = "col-12">
													<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Glavni razlog za (odluku o zapošljavanju) / konačna presuda"][$languageUser]; ?></p>
												</div>
											</div>
											<div class = "row pt-3">
												<div class = "col-lg-12">
													<div class = "row ">
														<div class = "col-12">
															<textarea class="textArreaStyle saveGeneralInfo" id="reasonRecommendationGeneral" rows="2" data-field_general="pca_reason_recommendation" data-pca_id = "<?php echo $pca_id;?>"><?php echo ( ($flagInfoEnpal == 1 AND $infoEnpalPCA["pca_reason_recommendation"] != '') ? $infoEnpalPCA["pca_reason_recommendation"] : null  );?></textarea>
														</div>
													</div>
												</div>
											</div>
											<div class = "row pt-3">
												<div class = "col-lg-12">
													<div id = "avgRating" style = "display: none;">
														
													</div>
												</div>
											</div>
										<!--
										General info END
									-->
								</div>
								<!--
										SCRIPT General info START
									-->
										<script>
											$(document).ready(function() {
												$('.selectpicker_option').selectpicker();
											});

											var timeoutIdGen;
											$('.saveGeneralInfo').on('input propertychange', function() {
												var kand_id_general = parseInt("<?php echo $kandidat_id; ?>");
												var kand_key_general = "<?php echo $candidate_key; ?>";
												var kand_nalog_id_general = parseInt("<?php echo $nalog_id; ?>");
												var pca_id_general = parseInt($(this).data("pca_id")); //ID odgovor
												var pap_id_general = parseInt("<?php echo $pap_id; ?>");
												var value_general = "";
												var field_general = $(this).data("field_general"); 
												if (field_general == "pca_comment") {
													value_general = $("#commentGeneral").val();
												} else if (field_general == "pca_reason_recommendation") {
													value_general = $("#reasonRecommendationGeneral").val();
												} else {
													value_general = $("#interviewerGeneral").val();
												}

												clearTimeout(timeoutIdGen);
												timeoutIdGen = setTimeout(function() {
													if(value_general == ""){
														if (field_general == "pca_comment") { 
															$("#commentGeneral").effect('shake' ,500);
														} else if (field_general == "pca_reason_recommendation") {
															$("#reasonRecommendationGeneral").effect('shake' ,500);
														} else {
															$("#interviewerGeneral").effect('shake' ,500);
														}
													}else{
														//getLoaderBig();
														$.ajax({
															url: 'ajax.php?action=updateGeneralInfoPCA',
															type: 'POST',
															data: {
																'kand_id_general':kand_id_general,
																'kand_nalog_id_general':kand_nalog_id_general,
																'pap_id_general':pap_id_general,
																'pca_id_general':pca_id_general,
																'field_general':field_general,
																'value_general':value_general
															},
															dataType: 'html',
															success: function(data){
																/*
																	AVG Rating START
																	*/
																	const objGI = JSON.parse(data);

																		if (objGI.status !== 102) {
																			if (objGI.avgUpdate == 1) {
																				
																				getCandidateAvgRatingEnpal(kand_id_general, kand_key_general, kand_nalog_id_general, pca_id_general);

																			} 
																		}

																	/*
																	AVG Rating END
																*/
															},
															error: function (xhr, ajaxOptions, thrownError) {
																alert(xhr.status);
																alert(thrownError);
															}
														});
													}
												}, 1000);
											});

											$('.saveGeneralRecommentation').on('change', function(){
												var kand_id_general = parseInt("<?php echo $kandidat_id; ?>");
												var kand_key_general = "<?php echo $candidate_key; ?>";
												var kand_nalog_id_general = parseInt("<?php echo $nalog_id; ?>");
												var pca_id_general = parseInt($(this).data("pca_id")); //ID odgovor
												var pap_id_general = parseInt("<?php echo $pap_id; ?>");
												var field_general = $(this).data("field_general"); 
												var	value_general = $("#recommendationGeneral").val();

												if(value_general == ""){
													$("#recommendationGeneral").effect('shake' ,500);
												}else{
													//getLoaderBig();
													$.ajax({
														url: 'ajax.php?action=updateGeneralInfoPCA',
														type: 'POST',
														data: {
															'kand_id_general':kand_id_general,
															'kand_nalog_id_general':kand_nalog_id_general,
															'pap_id_general':pap_id_general,
															'pca_id_general':pca_id_general,
															'field_general':field_general,
															'value_general':value_general
														},
														dataType: 'html',
														success: function(data){
															/*
																AVG Rating START
																*/
																const objGR = JSON.parse(data);

																	if (objGR.status !== 102) {
																		if (objGR.avgUpdate == 1) {
																			
																			getCandidateAvgRatingEnpal(kand_id_general, kand_key_general, kand_nalog_id_general, pca_id_general);

																		} 
																	}

																/*
																AVG Rating END
															*/
														},
														error: function (xhr, ajaxOptions, thrownError) {
															alert(xhr.status);
															alert(thrownError);
														}
													});
												}
											});
										</script>
									<!--
										SCRIPT General info END
								-->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php 
	break;

	case "candidateInsertInterview":
		$kandidat_id 	= $_POST["kandidat_id"];
		$pap_id 		= $_POST["pap_id"];
		$pca_id 		= $_POST["pca_id"];
		$type 			= $_POST["type"];
		$bar_id 		= $_POST["bar_id"];
		$nalog_id 		= $_POST["nalog_id"];
		$candidate_key 	= getCandidateKeyById($kandidat_id);
	?>
		<div class = "row">
			<div class = "col-12">
				<div class = "row align-items-center">
					<div class = "col-1 text-center goToProfile2 cursorPointer">
						<i class="fa fa-angle-left fa-3x" aria-hidden="true"></i>
					</div>
					<div class = "col-11 text-center">
						<p class = "text_color mb-0" style = "font-style: normal; font-weight: bold; font-size: 28px;"><?php echo $txtArray["Ocjenite intervju"][$languageUser]; ?></p>
						
					</div>
				</div>
				<script>
					$('.goToProfile2').on('click', function() {
						getLoaderBig();
						var readyKanId = "<?php echo $candidate_key; ?>";
						var type = parseInt("<?php echo $type; ?>");
						var bar_id = parseInt("<?php echo $bar_id; ?>");
						var nalog_id = parseInt("<?php echo $nalog_id; ?>");
						$.ajax({
							url: 'ajax.php?action=candidateProfile',
							type: 'POST',
							data: {
								'kandidat_id':readyKanId,
								'type':type,
								'bar_id':bar_id,
								'nalog_id':nalog_id
							},
							dataType: 'html',
							success: function(data){
								$("#candidateProfile").html(data);
								$( "#candidateInsertInterview" ).hide( 'slide', 250, function(){
									$( "#candidateInsertInterview" ).html( '' );
									$( "#candidateProfile" ).show( 'slide', 250);
									$("#edit_candidate_partner_data").unbind('click').bind('click', handleEditCandidatePartnerData);
								});
								removeLoader();
							},
							error: function (xhr, ajaxOptions, thrownError) {
								alert(xhr.status);
								alert(thrownError);
							}
						});
					});
				</script>
				<div class = "row gx-5 gy-3">
					<div class = "col-md-12 ">
						<div class = "row">
							<div class = "col-12">
								<div class = "px-5" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
									<div class = "row">
										<div class = "col-12">
											<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Opšti komentar"][$languageUser]; ?></p>
										</div>
									</div>
									<div class = "row pt-3">
										<div class = "col-lg-12">
											<div class = "row ">
												<div class = "col-12">
													<?php 
														//Provjera da li je vec odgovoreno
														$commentGeneral = getCommentPCA($kandidat_id, $pca_id);
														if($commentGeneral != 101 AND $commentGeneral != 102){
															$commentGeneral = $commentGeneral;
														}else{
															$commentGeneral = "";
														}
													?>
													<textarea class="textArreaStyle saveGeneralComment" id="commentGeneral" rows="2" data-pca_id = "<?php echo $pca_id;?>"><?php echo $commentGeneral;?></textarea>
												</div>
											</div>
										</div>
									</div>
									<!--
										Pitanja i ocjenjivanje START
										-->
										<div class = "row pt-5">
												<div class = "col-12">
													<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Pitanja i ocjenjivanje"][$languageUser]; ?></p>
												</div>
											</div>
											<div class = "row pt-3">
												<div class = "col-12">
													<!-- 
														Pitanja Start
														-->
															<?php 
																$appQuestions = array();
																$appQuestions =  getAppointmentQuestionForAppointmentArrayR($pap_id);

																if(count($appQuestions["count"]) != 0){
																	foreach($appQuestions["count"] AS $countAppQuestions){
																		?>

																			<!--
																				Jedna grupa Start 
																				-->
																					<?php 
																						if ($appQuestions["categoryStart"][$countAppQuestions] == 1 AND $appQuestions["categoryId"][$countAppQuestions] != 0) {
																							?>
																								<hr class="border border-secondary border-2 opacity-50 mb-2 <?php echo ($countAppQuestions != 0) ? "mt-5" : "mt-2";?>">
																								<div class = "row px-3 py-3 mb-3">
																									<div class = "col-lg-12">
																										<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 19px;"><?php echo $appQuestions["categoryName"][$countAppQuestions];?></p>
																									</div>
																								</div>
																							<?php 
																						}
																					?>
																				<!--
																				Jedna grupa END 
																			-->

																			<!--
																				Jedno pitanje Start 
																				-->
																					<div class = "row px-3 py-3 mb-3 rounded shadow-sm">
																						<div class = "col-12">
																							<?php 
																								$classText = "";
																								$classRating = "";
																								$classDropdown = "";

																								if ($appQuestions["pqu_has_rating"][$countAppQuestions] == 1 AND $appQuestions["pqu_has_dropdown"][$countAppQuestions] == 1 ) {
																									$classText = "col-lg-6";
																									$classRating = "col-lg-3";
																									$classDropdown = "col-lg-3";
																								} else if ($appQuestions["pqu_has_rating"][$countAppQuestions] == 1 AND $appQuestions["pqu_has_dropdown"][$countAppQuestions] == 0) {
																									$classText = "col-lg-7";
																									$classRating = "col-lg-5";
																									$classDropdown = "col-lg";
																								} else if ($appQuestions["pqu_has_rating"][$countAppQuestions] == 0 AND $appQuestions["pqu_has_dropdown"][$countAppQuestions] == 1) {
																									$classText = "col-lg-7";
																									$classRating = "col-lg";
																									$classDropdown = "col-lg-5";
																								} else {
																									$classText = "col-lg-12";
																									$classRating = "col-lg";
																									$classDropdown = "col-lg";
																								}
																							?>
																							<div class = "row align-items-center my-2">
																								<!--
																									Text Pitanja Start
																									-->
																										<div class = "<?php echo $classText; ?>">
																											<div class = "row align-items-center">
																												<!--<div class = "col-12">
																													<p class = "mb-0" style = "color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php //echo $txtArray["Pitanje"][$languageUser]; ?> #<?php //echo $countAppQuestions + 1;?></p>
																												</div>-->
																												<div class = "col-12">
																													<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;"><?php echo $appQuestions["pqu_question"][$countAppQuestions];?></p>
																												</div>
																											</div>
																										</div>
																									<!--
																									Text Pitanja END
																								-->
																								<?php 
																									if($appQuestions["pqu_has_rating"][$countAppQuestions] == 1) {
																										?>
																											<!--
																												Ocjena Pitanja Start
																												-->
																													<div class = "<?php echo $classRating; ?>">
																														<div class = "row align-items-center">
																															<div class = "col-12 text-center">
																																<?php 
																																	//Provjera da li je vec odgovoreno
																																	$ratingForQuestion = getRatingForAppointmentQuestion($kandidat_id, $appQuestions["papq_id"][$countAppQuestions]);
																																	$arrayCheckedValue = array(
																																		"1" => array(""),
																																		"2" => array(""),
																																		"3" => array(""),
																																		"4" => array(""),
																																		"5" => array("")
																																	);
																																	if($ratingForQuestion != 100 AND $ratingForQuestion != 101 AND $ratingForQuestion != 102){
																																		$arrayCheckedValue[$ratingForQuestion][0] = "checked";
																																	}else{
																																		$arrayCheckedValue["1"][0] = "";
																																	}
																																?>
																																<style>
																																	/* 
																																		Ocjenjivanje style START
																																		*/
																																			/* Ovo je dinamicni CSS - svaka iteracija svoj CSS */
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> {direction: rtl; margin: 0 auto;}
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> label {cursor: pointer; padding: 2px; margin: 0;}
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> .fa {color: #FDD878;}
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> input:not(:checked) ~ label:not([for=""]) > .fa-star {display: none;}
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> label:hover > .fa-star-o,
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> label:hover ~ label > .fa-star-o,
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> input:checked ~ label > .fa-star-o {display: none;}
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> label:hover > .fa-star,
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> label:hover ~ label > .fa-star,
																																			.stars<?php echo $appQuestions["papq_id"][$countAppQuestions]; ?> input:checked ~ label > .fa-star {display: inline-block !important;} 
																																		/* 
																																		Ocjenjivanje style END
																																	*/ 
																																</style>
																																<div class="stars<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																	<input type="radio" name="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>" id="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-5" hidden <?php echo $arrayCheckedValue["5"][0]; ?>>
																																	<label for="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-5" class = "ratingInsertValue" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-value="5" data-column_name="pra_rating" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																		<i class="fa fa-star fa-2x"></i>
																																		<i class="fa fa-star-o fa-2x"></i>
																																	</label>
																																	<input type="radio" name="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>" id="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-4" hidden <?php echo $arrayCheckedValue["4"][0]; ?>>
																																	<label for="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-4" class = "ratingInsertValue" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-value="4" data-column_name="pra_rating" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																		<i class="fa fa-star fa-2x"></i>
																																		<i class="fa fa-star-o fa-2x"></i>
																																	</label>
																																	<input type="radio" name="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>" id="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-3" hidden <?php echo $arrayCheckedValue["3"][0]; ?>>
																																	<label for="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-3" class = "ratingInsertValue" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-value="3" data-column_name="pra_rating" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																		<i class="fa fa-star fa-2x"></i>
																																		<i class="fa fa-star-o fa-2x"></i>
																																	</label>
																																	<input type="radio" name="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>" id="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-2" hidden <?php echo $arrayCheckedValue["2"][0]; ?>>
																																	<label for="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-2" class = "ratingInsertValue" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-value="2" data-column_name="pra_rating" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																		<i class="fa fa-star fa-2x"></i>
																																		<i class="fa fa-star-o fa-2x"></i>
																																	</label>
																																	<input type="radio" name="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>" id="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-1" hidden <?php echo $arrayCheckedValue["1"][0]; ?>>
																																	<label for="difficulty<?php echo $appQuestions["papq_id"][$countAppQuestions];?>-1" class = "ratingInsertValue" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-value="1" data-column_name="pra_rating" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																		<i class="fa fa-star fa-2x"></i>
																																		<i class="fa fa-star-o fa-2x"></i>
																																	</label>
																																</div>
																																<?php 
																																	unset($arrayCheckedValue);
																																?>
																															</div>
																														</div>
																													</div>
																												<!--
																												Ocjena Pitanja END
																											-->
																										<?php
																									}
																									
																									if($appQuestions["pqu_has_dropdown"][$countAppQuestions] == 1) {

																										$questionOptions = getQuestionOptionsArrayR($appQuestions["pqu_id"][$countAppQuestions]);
																										$valueOptionForQuestion = getOptionForAppointmentQuestion($kandidat_id, $appQuestions["papq_id"][$countAppQuestions]);

																										?>
																											<!--
																												Dropdown Pitanja Start
																												-->
																													<div class = "<?php echo $classDropdown; ?>">
																														<div class = "row align-items-center">
																															<div class = "col-12">
																																<select class="selectpicker col-12 selectpicker_option optionInsertValue" title="<?php echo $txtArray['Odaberite opciju'][$languageUser]; ?>" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-column_name="pra_options_value" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>">
																																	<?php 
																																		if (count($questionOptions["count"]) != 0) {

																																			foreach($questionOptions["count"] AS $countQuestionOptions) {

																																				?>

																																					<option 
																																						value="<?php echo $questionOptions["pqo_value"][$countQuestionOptions]; ?>" 
																																						<?php echo ( ($questionOptions["pqo_value"][$countQuestionOptions] == $valueOptionForQuestion ) ? 'selected' : ''); ?>
																																						<?php echo ( ($questionOptions["pqo_value_subtext"][$countQuestionOptions] != NULL) ? 'data-subtext="'.$questionOptions["pqo_value_subtext"][$countQuestionOptions].'"' : '' ); ?>
																																					>
																																						<?php echo $questionOptions["pqo_value_text"][$countQuestionOptions]; ?>
																																					</option>
																																				
																																				<?php

																																			}

																																		}
																																	?>
																																</select>
																															</div>
																														</div>
																													</div>
																												<!--
																												Dropdown Pitanja END
																											-->
																										<?php 
																									}
																								?>
																							</div>
																							<?php 
																								if($appQuestions["pqu_has_text"][$countAppQuestions] == 1) {
																									?>
																										<!--
																											Biljeska Pitanja Start
																											-->
																												<div class = "row align-items-center">
																													<div class = "col-lg-12">
																														<div class = "row align-items-center">
																															<div class = "col-12">
																																<?php 
																																	//Provjera da li je vec odgovoreno
																																	$commentForQuestion = getCommentForAppointmentQuestion($kandidat_id, $appQuestions["papq_id"][$countAppQuestions]);
																																	if($commentForQuestion != 100 AND $commentForQuestion != 101 AND $commentForQuestion != 102){
																																		$commentsDefaultRows = 2;
																																		$commentForQuestion = $commentForQuestion;
																																	}else{
																																		$commentsDefaultRows = 2;
																																		$commentForQuestion = "";
																																	}
																																?>
																																<textarea class="textArreaStyle commentInsertValue" rows="<?php echo $commentsDefaultRows; ?>" data-candidate_id="<?php echo $kandidat_id;?>" data-candidate_key="<?php echo $candidate_key;?>" data-nalog_id="<?php echo $nalog_id;?>" data-column_name="pra_comment" data-pca_id="<?php echo $pca_id;?>" data-papq_id="<?php echo $appQuestions["papq_id"][$countAppQuestions];?>"><?php echo $commentForQuestion;?></textarea>
																															</div>
																														</div>
																													</div>
																												</div>
																											<!--
																											Biljeska Pitanja END
																										-->
																									<?php 
																								}
																							?>
																						</div>
																					</div>
																				<!--
																				Jedno pitanje END 
																			-->

																		<?php 
																	}
																}

																unset($appQuestions);
															?>
														<!-- 
														Pitanja End
													-->

													<!-- 
														SCRIPT PITANJA START 
														-->
														
															<script>
																function valueInsertUpdateForCandidateQuestion(candidateId, candidateKey, candidateNalog, papqId, pcaId, columnName, value){

																	var candidateIdVar = parseInt(candidateId);
																	var candidateKeyVar = candidateKey;
																	var candidateNalogVar = parseInt(candidateNalog);
																	var papqIdVar = parseInt(papqId);
																	var pcaIdVar = parseInt(pcaId);
																	var columnNameVar = columnName;

																	if (columnNameVar === "pra_rating") {
																		var valueVar = parseInt(value);
																	} else if (columnNameVar === "pra_options_value") {
																		var valueVar = parseInt(value);
																	} else {
																		var valueVar = value;
																	}

																	//alert(" candidateIdVar:"+candidateIdVar+" candidateKeyVar:"+candidateKeyVar+" candidateNalogVar:"+candidateNalogVar+" papqIdVar:"+papqIdVar+" pcaIdVar:"+pcaIdVar+" columnNameVar:"+columnNameVar+" valueVar:"+valueVar);
																	/*
																		backend request START
																		*/
																			$.ajax({
																				url: 'ajax.php?action=insert_update_candidate_question_new',
																				type: 'POST',
																				data: {
																					'candidateIdVar': candidateIdVar,
																					'candidateNalogVar': candidateNalogVar,
																					'papqIdVar': papqIdVar,
																					'pcaIdVar': pcaIdVar,
																					'columnNameVar': columnNameVar,
																					'valueVar': valueVar
																				},
																				dataType: 'html',
																				success: function(data){
																					/*
																						AVG Rating START
																						*/
																							const obj = JSON.parse(data);

																							if (obj.status !== 102) {
																								if (obj.avgUpdate == 1) {
																									
																									getCandidateAvgRating(1);

																								} 
																							}

																						/*
																						AVG Rating END
																					*/
																				},
																				error: function (xhr, ajaxOptions, thrownError) {
																					alert(xhr.status);
																					alert(thrownError);
																				}
																			});
																		/*
																		backend request END
																	*/

																}

																$(".ratingInsertValue").on("click",function() {

																	var candidate_id = $(this).data("candidate_id");
																	var candidate_key = $(this).data("candidate_key");
																	var nalog_id = $(this).data("nalog_id");
																	var papq_id = $(this).data("papq_id");
																	var pca_id = $(this).data("pca_id");
																	var column_name = $(this).data("column_name");
																	var value = $(this).data("value");

																	valueInsertUpdateForCandidateQuestion(candidate_id, candidate_key, nalog_id, papq_id, pca_id, column_name, value); 

																});

																var timeoutIdComment;
																$('.commentInsertValue').on('input propertychange', function() {
																	var candidate_id = $(this).data("candidate_id");
																	var candidate_key = $(this).data("candidate_key");
																	var nalog_id = $(this).data("nalog_id");
																	var papq_id = $(this).data("papq_id");
																	var pca_id = $(this).data("pca_id");
																	var column_name = $(this).data("column_name");
																	var value = $(this).val();

																	clearTimeout(timeoutIdComment);
																	timeoutIdComment = setTimeout(function() {
																		if(value == ""){
																			$("#comment"+papq_id+"").effect('shake' ,500);
																		}else{
																			valueInsertUpdateForCandidateQuestion(candidate_id, candidate_key, nalog_id, papq_id, pca_id, column_name, value); 
																		}
																	}, 1000);

																});

																$(".optionInsertValue").on("change",function() {

																	var candidate_id = $(this).data("candidate_id");
																	var candidate_key = $(this).data("candidate_key");
																	var nalog_id = $(this).data("nalog_id");
																	var papq_id = $(this).data("papq_id");
																	var pca_id = $(this).data("pca_id");
																	var column_name = $(this).data("column_name");
																	var value = $(this).val();

																	valueInsertUpdateForCandidateQuestion(candidate_id, candidate_key, nalog_id, papq_id, pca_id, column_name, value); 

																});

															</script>
														<!--
															SCRIPT PITANJA START
													-->
												</div>
											</div>
										<!--
										Pitanja i ocjenjivanje END
									-->
									<div class = "row pt-2">
										<div class = "col-12">
											<div id = "avgRating" style = "display: none;">
												
											</div>
										</div>
									</div>
									<script>
										function getCandidateAvgRating(reminder_execute) {
											var kanIdRating = parseInt("<?php echo $kandidat_id; ?>");
											var pca_id = parseInt("<?php echo $pca_id; ?>");
											var kanIdRatingKey = "<?php echo $candidate_key; ?>";
											var nalogIdRating = parseInt("<?php echo $nalog_id; ?>");
											$.ajax({
												url: 'ajax.php?action=candidateAvgRating',
												type: 'POST',
												data: {
													'kanIdAvgRat':kanIdRating,
													'pca_id':pca_id,
												},
												dataType: 'html',
												success: function(data){
													if(data != ""){
														if (reminder_execute === 1) {
															getAccessControl(nalogIdRating, kanIdRatingKey, null, 1, function(response_access_control){
																var reminder_status 			= response_access_control['status'];
																var reminder_id 				= response_access_control['id'];
																var reminder_assigned_user 		= response_access_control['assigned'];
																var reminder_is_logged_user 	= response_access_control['isLogged'];
																var reminder_response_message	= response_access_control['message'];
																var reminder_response_title		= response_access_control['title'];

																if((reminder_status == 101) || reminder_status == 1 || (reminder_status == 2 && reminder_is_logged_user == 1)){
																	//alert('Uradi sve normalno');
																	
																	if(reminder_status == 2 || reminder_status == 1){
																		updateReminderStatus(reminder_id, 3, function(response_update_reminder){
																			var update_status 	= response_update_reminder["status"];
																			var update_message 	= response_update_reminder["message"];
																			if(update_status == 3){
																				showToast(update_message, reminder_response_title);
																			}
																		});
																	}
																}
															});
														} 
														
														$("#avgRating").html(data);
														$( "#avgRating" ).show( 'slide', 10);
													}
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
										}; 
										$(document).ready(function(){
											$('.selectpicker_option').selectpicker();
											getCandidateAvgRating(0);
										});
										var timeoutIdGen;
										$('.saveGeneralComment').on('input propertychange', function() {
											var kanIdGenComment = parseInt("<?php echo $kandidat_id; ?>");
											var pca_id = parseInt($(this).data("pca_id")); //ID odgovor
											var kanGenComment = $("#commentGeneral").val(); //Vrijednost komentara
											clearTimeout(timeoutIdGen);
											timeoutIdGen = setTimeout(function() {
												if(kanGenComment == ""){
													$("#commentGeneral").effect('shake' ,500);
												}else{
													//getLoaderBig();
													$.ajax({
														url: 'ajax.php?action=candidateCommentGen',
														type: 'POST',
														data: {
															'kanIdComment':kanIdGenComment,
															'pca_id':pca_id,
															'kanComment':kanGenComment
														},
														dataType: 'html',
														success: function(data){
														},
														error: function (xhr, ajaxOptions, thrownError) {
															alert(xhr.status);
															alert(thrownError);
														}
													});
												}
											}, 1000);
										});
									</script>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
	<?php
	break;
	
	case "insert_update_candidate_question":

		$candidateIdVar = intval($_POST["candidateIdVar"]);
		$candidateNalogVar = intval($_POST["candidateNalogVar"]);
		$papqIdVar = intval($_POST["papqIdVar"]);
		$pcaIdVar = intval($_POST["pcaIdVar"]);
		$columnNameVar = $_POST["columnNameVar"];
		$valueVar = $_POST["valueVar"];

		$functionResults = valueInsertUpdateForCandidateQuestionArrayR($candidateIdVar, $candidateNalogVar, $papqIdVar, $pcaIdVar, $columnNameVar, $valueVar);
		
		echo json_encode($functionResults);
		
		unset($functionResults);
	break;

	case "insert_update_candidate_question_new":
		$candidateIdVar = intval($_POST["candidateIdVar"]);
		$candidateNalogVar = intval($_POST["candidateNalogVar"]);
		$papqIdVar = intval($_POST["papqIdVar"]);
		$pcaIdVar = intval($_POST["pcaIdVar"]);
		$columnNameVar = $_POST["columnNameVar"];
		$valueVar = $_POST["valueVar"];

		$functionResults = valueInsertUpdateForCandidateQuestionNewArrayR($candidateIdVar, $candidateNalogVar, $papqIdVar, $pcaIdVar, $columnNameVar, $valueVar);
		
		echo json_encode($functionResults);
		
		unset($functionResults);
	break; 

	case "candidateRating":
		$kanIdRating = intval($_POST["kanIdRating"]);
		$value_rating = intval($_POST["value_rating"]);
		$papq_id = intval($_POST["papq_id"]);
		$pca_id = intval($_POST["pca_id"]);
		
		$functionResults = ratingInsertUpdate($kanIdRating, $papq_id, $pca_id, $value_rating);
		
		echo $functionResults["avgUpdate"][0].",".$functionResults["action"][0];
		
		unset($functionResults);
	break;
	
	case "candidateCommentGen":
		$kanIdComment = intval($_POST["kanIdComment"]);
		$kanComment = $_POST["kanComment"];
		$pca_id = intval($_POST["pca_id"]);
		
		$functionResults = updateCommentPCA($kanIdComment, $pca_id, $kanComment);
		
		echo $functionResults;
	break;

	case "updateGeneralInfoPCA":
		$kand_id_general = intval($_POST["kand_id_general"]);
		$kand_nalog_id_general = intval($_POST["kand_nalog_id_general"]);
		$pap_id_general = intval($_POST["pap_id_general"]);
		$pca_id_general = intval($_POST["pca_id_general"]);
		$field_general = $_POST["field_general"]; 
		$value_general = $_POST["value_general"]; 

		$functionResults = updateGeneralInfoPCAArrayR($kand_id_general, $kand_nalog_id_general, $pap_id_general, $pca_id_general, $field_general, $value_general);

		echo json_encode($functionResults);
		unset($functionResults);
	break;
	
	case "candidateComment":
		$kanIdComment = intval($_POST["kanIdComment"]);
		$kanComment = $_POST["kanComment"];
		$papq_id = intval($_POST["papq_id"]);
		
		$functionResults = commentInsertUpdate($kanIdComment, $papq_id, $kanComment);
		
		echo $functionResults;
	break;
	
	case "candidateAvgRatingEnpal":
		$candidateIdVar = intval($_POST["candidateIdVar"]);
		$pcaIdVar = intval($_POST["pcaIdVar"]);

		$functionResults = getAvgRatingForCandidat($candidateIdVar, $pcaIdVar);
		if($functionResults != 0 AND $functionResults != 102){
			$arrayCheckedValue = array(
				"1" => array("fa fa-star-o"),
				"2" => array("fa fa-star-o"),
				"3" => array("fa fa-star-o"), 
				"4" => array("fa fa-star-o")
			);
			for($i = 1; $i <= $functionResults; $i++){
				$arrayCheckedValue[$i][0] = "fa fa-star";
			}
	?>	

		<hr class="border border-secondary border-2 opacity-50 mb-2 mt-2">
		<div class = "row py-3">
			<div class = "col-12 text-center">
				<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 22px;"><?php echo $txtArray["Ukupna ocjena"][$languageUser]; ?></p>
			</div>
		</div>
		<div class = "row">
			<div class = "col-12 text-center">
				<ul class=" list-group list-group-horizontal justify-content-center">
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["1"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["2"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["3"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["4"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
				</ul>
			</div>
		</div>

	<?php 
			unset($arrayCheckedValue);
		}else{
			echo "";
		}
	break; 

	case "candidateAvgRating":
		$kanIdAvgRat = intval($_POST["kanIdAvgRat"]);
		$pca_id = intval($_POST["pca_id"]);
		
		$functionResults = getAvgRatingForCandidat($kanIdAvgRat, $pca_id);
		if($functionResults != 0 AND $functionResults != 102){
			$arrayCheckedValue = array(
				"1" => array("fa fa-star-o"),
				"2" => array("fa fa-star-o"),
				"3" => array("fa fa-star-o"),
				"4" => array("fa fa-star-o"),
				"5" => array("fa fa-star-o")
			);
			for($i = 1; $i <= $functionResults; $i++){
				$arrayCheckedValue[$i][0] = "fa fa-star";
			}
		?>
		<div class = "row pb-3">
			<div class = "col-12 text-center">
				<p class = "mb-0" style = "color: #1F2E45; font-style: normal; font-weight: 600; font-size: 22px;"><?php echo $txtArray["Ukupna ocjena"][$languageUser]; ?></p>
			</div>
		</div>
		<div class = "row">
			<div class = "col-12 text-center">
				<ul class=" list-group list-group-horizontal justify-content-center">
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["1"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["2"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["3"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["4"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["5"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
				</ul>
			</div>
		</div>
	<?php 
			unset($arrayCheckedValue);
		}else{
			echo "";
		}
	break;
	
	case "candidatMaxAvgRating":
		$kandidatId = intval($_POST["kandidatId"]);
		$nalogIdd = intval($_POST["nalogId"]);
		$nalogId = $nalogIdd;
		$isEnpal = getCompanyAccessR($nalogId);

		if($kandidatId != 0 AND $nalogId != 0){
			$query = $db->prepare("
				SELECT 
					MAX(pca_id) AS pcaId
				FROM 
					idk_pp_cand_appts pca
				INNER JOIN 
					idk_pp_appointments pap
				ON 
					pca.pca_appointment_id = pap.pap_id
				WHERE 
					pca.pca_kandidat_id = :pca_kandidat_id
					AND 
					pap.pap_nalog_id = :pap_nalog_id
			");
			$query->execute(array(
				':pca_kandidat_id' => $kandidatId,
				':pap_nalog_id' => $nalogId
			));
			$row = $query->fetch();
			$pcaId = intval($row["pcaId"]);
			
			$functionResults = getAvgRatingForCandidat($kandidatId, $pcaId);
			if($functionResults != 0 AND $functionResults != 102){

				if($isEnpal == 1){
					$arrayCheckedValue = array(
						"1" => array("fa fa-star-o"),
						"2" => array("fa fa-star-o"),
						"3" => array("fa fa-star-o"), 
						"4" => array("fa fa-star-o")
					);
				}else{
					$arrayCheckedValue = array(
						"1" => array("fa fa-star-o"),
						"2" => array("fa fa-star-o"),
						"3" => array("fa fa-star-o"),
						"4" => array("fa fa-star-o"),
						"5" => array("fa fa-star-o")
					);
				}

				for($i = 1; $i <= $functionResults; $i++){
					$arrayCheckedValue[$i][0] = "fa fa-star";
				}
		?>
				<ul class=" list-group list-group-horizontal justify-content-center">
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["1"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["2"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["3"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["4"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<?php if($isEnpal != 1){ ?>
						<li class="list-group-item border-0 px-1"><i class="<?php echo $arrayCheckedValue["5"][0]; ?> fa-3x" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<?php } ?>		
				</ul>
		<?php 
				unset($arrayCheckedValue);
			}else{
		?>
				<ul class=" list-group list-group-horizontal justify-content-center">
					<li class="list-group-item border-0 px-1"><i class="fa fa-star-o" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="fa fa-star-o" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="fa fa-star-o" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<li class="list-group-item border-0 px-1"><i class="fa fa-star-o" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<?php if($isEnpal != 1){ ?>
						<li class="list-group-item border-0 px-1"><i class="fa fa-star-o" style = "color: #FDD878;" aria-hidden="true"></i></li>
					<?php } ?>
				</ul>
		<?php 
			}
		}else{
		?>
			<ul class=" list-group list-group-horizontal justify-content-center">
				<li class="list-group-item border-0 px-1"><i class="fa fa-star-o" style = "color: #FDD878;" aria-hidden="true"></i></li>
				<li class="list-group-item border-0 px-1"><i class="fa fa-star-o" style = "color: #FDD878;" aria-hidden="true"></i></li>
				<li class="list-group-item border-0 px-1"><i class="fa fa-star-o" style = "color: #FDD878;" aria-hidden="true"></i></li>
				<li class="list-group-item border-0 px-1"><i class="fa fa-star-o" style = "color: #FDD878;" aria-hidden="true"></i></li>
				<?php if($isEnpal != 1){ ?>
					<li class="list-group-item border-0 px-1"><i class="fa fa-star-o" style = "color: #FDD878;" aria-hidden="true"></i></li>
				<?php } ?>
			</ul>
		<?php 
		}
	break;
	case "load_modal_manage_documents":
		$nalog_id 		= $_REQUEST['nalog_id'];
		$partner_id 	= $_REQUEST['partner_id'];
		$candidate_key 	= $_REQUEST['candidate_id'];
		$location	 	= $_REQUEST['location'];
		$candidate_id 	= getCandidateIdByKey($candidate_key);

		?>
		
		<div class="modal fade" id="modalManageDocuments" aria-hidden="true" aria-labelledby="modalManageDocuments" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content border-0">
					<div class="modal-header text-center">
						<h5 class="modal-title w-100" id="modalOdbijLabel"><?php echo $txtArray["Označavanje slanja ugovora"][$languageUser]; ?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="row pt-1 my-3 justify-content-center">
							<div class="col-10">
								<?php
									// U slučaju da su podaci već uneseni, vraćamo formu koja sadrži te podatke ali je readonly
									$contractSentInfo = checkContractSentArrayR($candidate_key);
									$contractSentInfoExists = false;
									$contractSentDate = "";
									$contractSentCode = "";
									$contractSentLink = "";
									if(count($contractSentInfo) > 0){
										$contractSentInfoExists = true;
										$contractSentDate = $contractSentInfo["cs_sent_date"];
										$contractSentCode = $contractSentInfo["cs_tracking_code"];
										$contractSentLink = $contractSentInfo["cs_tracking_link"];
									}

								?>
								<form
									candidate_id="<?php echo $candidate_key; ?>"
									candidate_key="<?php echo $candidate_key; ?>"
									nalog_id="<?php echo $nalog_id; ?>"
									partner_id="<?php echo $partner_id; ?>"
									location="<?php echo $location; ?>"
									id="contractSentForm"
									action=""
								>
									<div class="mb-3">
										<label for="contractDateSent" class="form-label"><?php echo $txtArray["Datum slanja ugovora"][$languageUser] ?></label>
										<input 
											<?php echo "value='$contractSentDate'"; ?>  
											<?php if($contractSentInfoExists) echo "readonly"; ?>  
											required type="date" class="form-control" id="contractDateSent">
									</div>
									<div class="mb-3">
										<label for="contractTrackingCode" class="form-label"><?php echo $txtArray["Tracking code ugovora"][$languageUser] ?></label>
										<input 
											<?php echo "value='$contractSentCode'"; ?>  
											<?php if($contractSentInfoExists) echo "readonly"; ?>  
											required type="text" class="form-control" id="contractTrackingCode">
									</div>
									<div class="mb-3">
										<label for="contractTrackingLink" class="form-label"><?php echo $txtArray["Link za provjeru"][$languageUser] ?></label>
										<input 
											<?php echo "value='$contractSentLink'"; ?>  
											<?php if($contractSentInfoExists) echo "readonly"; ?>  
											required type="text" class="form-control" id="contractTrackingLink">
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button 
							type="submit" 
							class="btn btn-primary px-4"
							form="contractSentForm"
							id="submitContractSentForm"
							<?php if($contractSentInfoExists) echo "disabled"; ?>
						>
							<?php echo $txtArray["Završi"][$languageUser] ?>
						</button>
					</div>
				</div>
			</div>
		</div>
		<?php
	break;
	case 'get_document':
		
		$nalog_id 		= $_REQUEST['nalog_id'];
		$candidate_key 	= $_REQUEST['candidate_id'];
		$partner_id 	= $_REQUEST['partner_id'];
		$is_uploaded 	= $_REQUEST['is_uploaded'];
		$document_type 	= $_REQUEST['document_type'];
		$location	 	= $_REQUEST['location'];
		$candidate_id  	= getCandidateIdByKey($candidate_key);
		if($is_uploaded){
			if($document_type == "cs" || $document_type == "cus"){
				$contract_is_signed = 0;
				if($document_type == "cs"){
					$contract_is_signed = 1;
				}
				
				$query_get_contract = $db -> prepare('
					SELECT kc_file_name
					FROM idk_kandidati_contracts
					WHERE kc_candidate_id = :candidate_id
					AND kc_nalog_id = :nalog_id
					AND kc_partner_id = :partner_id
					AND kc_signed = :contract_is_signed
					AND kc_visibility_status = 2
					ORDER BY kc_id DESC
				');
				$query_get_contract -> execute(array(
					':candidate_id' 			=> $candidate_id,
					':nalog_id' 			=> $nalog_id,
					':partner_id' 			=> $partner_id,
					':contract_is_signed' 	=> $contract_is_signed
				));

				$row_get_contract = $query_get_contract -> fetch();
				$file_location = 'files/candidate_contracts/'.$row_get_contract['kc_file_name'];
			}
		?>
		<div class = "row" id = "document_management_row">
			<div class = "col-4">
				<a href="<?php echo $file_location; ?>" download><i class="fa fa-arrow-down fa-3x document_download" aria-hidden="true" file_name = "<?php echo $file_location; ?>"></i></a><br>
				<?php echo $txtArray['Skini'][$languageUser]; ?>
			</div>
			<div class = "col-4">
				<i class="fa fa-eye fa-3x document_view" aria-hidden="true" file_name = "<?php echo $file_location; ?>"></i><br>
				<?php echo $txtArray['Pregled'][$languageUser]; ?>
			</div>
			<div class = "col-4">
				<i class="fa fa-trash fa-3x document_archive" aria-hidden="true"></i><br>
				<?php echo $txtArray['Obriši'][$languageUser]; ?>
			</div>
		</div>
		<div class = "row" id = "document_delete_row" style = "text-align:center; display:none;">
			<a><?php echo $txtArray['Jeste li sigurni da želite obrisati ovaj dokument?'][$languageUser]; ?></a><br><br>
			<div class = "col-6">
				<i class="fa fa-thumbs-o-up fa-3x confirm_document_delete" aria-hidden="true"></i><br>
				<?php echo $txtArray['Da'][$languageUser]; ?>
			</div>
			<div class = "col-6">
				<i class="fa fa-thumbs-o-down fa-3x disconfirm_document_delete" aria-hidden="true"></i><br>
				<?php echo $txtArray['Ne'][$languageUser]; ?>
			</div>
		</div>
		<?php
		}
		else{
		?>
		<div class = "row">
			<div class = "col-6">
				<label style = "margin-top:0px;" for="upload_document"><i class="fa fa-search fa-3x document_search" aria-hidden="true"></i>
					<br><?php echo $txtArray['Odaberi dokument'][$languageUser]; ?>
				</label>
				<input id = "upload_document" type="file" style = "display:none">
			</div>
			<div class = "col-6">
				<i id = "btn_upload_document" class="fa fa-upload fa-3x document_upload_not_allowed" aria-hidden="true" location = "<?php echo $location; ?>" nalog_id = "<?php  echo $nalog_id; ?>" candidate_id = "<?php  echo $candidate_key; ?>" partner_id = "<?php  echo $partner_id; ?>" document_type = "<?php  echo $document_type; ?>" ></i>
				<br><?php echo $txtArray['Upload'][$languageUser]; ?>
			</div>
			<div id = "document_upload_alert" class = "col-12">
				<p id = "alert_exceeds_size" class = "error_message" style = "display:none;"> <?php echo $txtArray['5 MB je najveća dozvoljena veličina fajla'][$languageUser]; ?></p>
				<p id = "alert_invalid_extension" class = "error_message" style = "display:none;"> <?php echo $txtArray['Odabrani tip fajla nije dozvoljen'][$languageUser]; ?></p>
				
			</div>
		</div>
		<?php
		}

	break;
	case "upload_document":
		$nalog_id 			= $_REQUEST['nalog_id'];
		$candidate_id 		= $_REQUEST['candidate_id'];
		$partner_id 		= $_REQUEST['partner_id'];
		$document_type 		= $_REQUEST['document_type'];
		$uploaded_contract 	= $_FILES['uploaded_contract'];
		$candidate_id 		= getCandidateIdByKey($candidate_id);
	
		if($document_type == "cs" || $document_type == "cus"){
			$location = $_SERVER['DOCUMENT_ROOT']."/files/candidate_contracts/";
			$vrijeme = date("y-m-d_H:i");
			$ext = explode(".", $uploaded_contract['name']);
			$ext = strtolower(end($ext));
			$file_name = "UG".$candidate_id.$vrijeme.".".$ext;
			move_uploaded_file($uploaded_contract["tmp_name"], $location.$file_name);	
			
			$is_signed_int = 0;
			$status_prijave_to_be = 8;
			if($document_type == "cs"){
				$status_prijave_to_be = 9;
				$is_signed_int = 1;
			}
			$query_update_status_prijave = $db -> prepare('
				UPDATE idk_kandidati
				SET kandidat_status_prijave = :status_prijave_to_be
				WHERE kandidat_id = :candidate_id
			');
			$query_update_status_prijave -> execute(array(
				':candidate_id' => $candidate_id,
				':status_prijave_to_be' => $status_prijave_to_be
			));
			
			$query_insert_contract = $db -> prepare('
				INSERT INTO idk_kandidati_contracts (kc_file_name, kc_candidate_id, kc_source, kc_user_id, kc_upload_time, kc_nalog_id, kc_partner_id, kc_signed, kc_visibility_status)
				VALUES (:file_name, :candidate_id, 2, :user_id, now(), :nalog_id, :partner_id, :is_signed, 2)

			');
			
			$query_insert_contract -> execute(array(
				':file_name' => $file_name,
				':candidate_id' => $candidate_id,
				':user_id' => $userId,
				':nalog_id' => $nalog_id,
				':partner_id' => $partner_id,
				':is_signed' => $is_signed_int
			));
			$query_get_project_id = $db -> prepare('
				SELECT project_id
				FROM idk_projects
				WHERE project_name LIKE ("%- Ugovor%")
				AND project_nalogid = :nalog_id
			');
			
			$query_get_project_id -> execute(array(':nalog_id' => $nalog_id));
			
			$row_get_project_id = $query_get_project_id -> fetch();
			
			$project_id = $row_get_project_id['project_id'];

			addToLogsStatusPrijave($project_id, $project_id, $status_prijave_to_be, $candidate_id, 2);
		}
		
	break;
	case "get_access_control":

		$reminder_type 	= $_REQUEST['reminder_type'];
		$candidate_id 	= $_REQUEST['candidate_id'];
		$partner_id 	= $_REQUEST['partner_id'];
		$nalog_id 		= $_REQUEST['nalog_id'];
		$doc_id 		= $_REQUEST['doc_id'];

		if($doc_id == 0){
			$array_to_encode = getAccessControlArrayR($candidate_id, $nalog_id, $partner_id, $reminder_type);
		}else{
			$array_to_encode = getAccessControlArrayR($candidate_id, $nalog_id, $partner_id, $reminder_type, $doc_id);
		}
		
		echo json_encode($array_to_encode);
		unset($array_to_encode);
	break;
	
	case "update_reminder_status":

		$reminder_id 	= $_REQUEST['reminder_id'];
		$status 		= $_REQUEST['status'];
		
		$array_to_encode = updateReminderStatus($reminder_id, $status);
		
		echo json_encode($array_to_encode);
		unset($array_to_encode);
	break;
	
	case "check_contract_sent":

		$canKey 	= $_REQUEST['canKey'];
		
		$result = checkContractSentArrayR($canKey);

		$response = [];
		$response["contractInfoExists"] = false;
		
		if(count($result) > 0){
			$response["contractInfoExists"] = true;
		}

		echo json_encode($response);
		unset($result);
	break;

	case "insert_contract_sent":

		$canKey 			= $_REQUEST['can_key'];
		$trackingCode 		= $_REQUEST['tracking_code'];
		$linkTrackingCode	= $_REQUEST['link_tracking_code'];
		$sentDate			= $_REQUEST['sent_date'];
		
		$response = insertContractSentR($canKey, $trackingCode, $linkTrackingCode, $sentDate);
		
		echo $response;
		
	break;

	case "get_reminders_by_type":
	
		$selected_reminders = $_REQUEST['selected_reminders'];
		// var_dump($selected_reminders);
		if(is_null($selected_reminders)){
			$seleted_reminders = 0;
			?>
			<div class = "row pt-1 me-5 my-3">
				<div class = "col-12 text-center">
					<p  style = "color: #8E97A3; font-weight: 600; font-size:16px;" class = "mb-0"><?php echo $txtArray["Odaberi tip u filteru"][$languageUser]; ?></p>
				</div>
			</div>
			<?php 
		}
		else{
			$selected_reminders = implode(',', $selected_reminders);
			
			// var_dump($selected_reminders);
			$reminderCnt = 0;	//Ovdje je bilo napisano $reminderCnt == 0; pa sam ispravio jer se pojavilo u error logu			
			$remindersResult = getRemindersArrayR($selected_reminders);
			$remindersCnt = count($remindersResult["count"]);
			if($remindersCnt != 0){
				
				foreach($remindersResult["count"] AS $reminderCountVal){
					// rr - remindersresult - DA se u ostatku koda ne bi gdje prekucala koja varijabla
					$rrReminderId 		= $remindersResult["reminderId"][$reminderCountVal];
					$rrReminderText 	= $remindersResult["reminderText"][$reminderCountVal];
					$rrReminderType 	= $remindersResult["reminderType"][$reminderCountVal];
					$rrCandidateId 		= $remindersResult["candidateId"][$reminderCountVal];
					$rrCandidateCheck 	= $remindersResult["candidateCheck"][$reminderCountVal];
					$rrCandidateFname 	= $remindersResult["candidateFname"][$reminderCountVal];
					$rrCandidateLname 	= $remindersResult["candidateLname"][$reminderCountVal];
					$rrReminderStatus 	= $remindersResult["reminderStatus"][$reminderCountVal];
					$rrReminderDate		= $remindersResult["reminderDate"][$reminderCountVal];
					$rrNalogId 			= $remindersResult["nalogId"][$reminderCountVal];
					$rrPartnerId 		= $remindersResult["partnerId"][$reminderCountVal];
					$reminderStatusIcon = "";
					$reminderStatusText = "";
					$reminderBgColor 	= "";
					if($rrReminderStatus == 1){
						$reminderStatusIcon = '<i class="fa fa-envelope" aria-hidden="true"></i>';
						$reminderStatusText = $txtArray['Neprihvaćen reminder'][$languageUser];
						$reminderBgColor = "#1d84c012";
					}else{
						$reminderStatusIcon = '<i class="fa fa-envelope-open" aria-hidden="true"></i>';
						$reminderStatusText = $txtArray['Prihvaćen reminder'][$languageUser];
						$reminderBgColor = "#c01d1d12";
					}
					?>
					<div class = "row pt-1 me-5 my-3">
						<div class = "col-12">
							<div class = "px-3 py-2 checkReminder" data-rrnalogid="<?php echo $rrNalogId; ?>" data-rrpartnerid="<?php echo $rrPartnerId; ?>" data-rrcandidatecheck="<?php echo $rrCandidateCheck; ?>" data-rrremindertype="<?php echo $rrReminderType; ?>" data-rrreminderId="<?php echo $rrReminderId; ?>" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 10px; background-color: <?php echo $reminderBgColor; ?>;">
								<div class = "row align-items-center">
									<div class = "col-2 text-center">
										<button class="btn d-inline" style= "border-radius:50%; width:50px; height: 50px; background: #919aa6; color: #fff; font-weight: 600;" disabled>
											<?php echo strtoupper(substr($rrCandidateFname, 0,1)."".substr($rrCandidateLname, 0,1)); ?>
										</button>
									</div>
									<div class = "col-10">
										<div class = "d-inline">
											<div class="row align-items-center">
												<div class="col-lg-12">
													<p  style = "color: #1F2E45; font-weight: 600; font-size:20px;" class = "mb-0"><?php echo $rrReminderText; ?></p>
												</div>
											</div>
											<div class="row align-items-center">
												<div class="col-lg-12">
													<p  style = "color: #8E97A3; font-weight: normal; font-size:16px;" class = "mb-0"><?php echo $rrCandidateFname." ".$rrCandidateLname; ?></p>
												</div>
											</div>
											<hr>
											<div class="row align-items-center">
												<div class="col-lg-1">
													<i class="fa fa-calendar" aria-hidden="true"></i>
												</div>
												<div class="col-lg-5">
													<p  style = "color: #8E97A3; font-weight: normal; font-size:14px;" class = "mb-0 remDate<?php echo $rrReminderId; ?>"><?php echo $rrReminderDate; ?></p>
												</div>
												<div class="col-lg-1 remIcon<?php echo $rrReminderId; ?>">
													<?php echo $reminderStatusIcon; ?>
												</div>
												<div class="col-lg-5">
													<p  style = "color: #8E97A3; font-weight: normal; font-size:14px;" class = "mb-0 remStatus<?php echo $rrReminderId; ?>"><?php echo $reminderStatusText; ?></p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php 
				}	
			}
			else{
				?>
				<div class = "row pt-1 me-5 my-3">
					<div class = "col-12 text-center">
						<p  style = "color: #8E97A3; font-weight: 600; font-size:16px;" class = "mb-0"><?php echo $txtArray["Nema novih obavijesti!"][$languageUser]; ?></p>
					</div>
				</div>
				<?php 
			}
			unset($remindersResult);
			
		}
	break;
	case "get_search_candidates":
	
		$search_text = $_REQUEST['search_text'];

		$query_get_search_candidates = $db -> prepare("
			SELECT
				kan.kandidat_id,
				n.nalog_naziv,
				kan.kandidat_status_prijave,
				kan.kandidat_ime,
				kan.kandidat_prezime,
				kan.kandidat_check,
				kan.kandidat_nalog_id,
				com.company_name,
				0 as is_rejected
			FROM
				(
				SELECT
					sqkan.kandidat_id,
					sqkan.kandidat_nalog_id,
					sqkan.kandidat_status_prijave, 
					sqkan.kandidat_ime,
					sqkan.kandidat_prezime,
					sqkan.kandidat_check
				FROM
					idk_kandidati sqkan
				WHERE 
				CONCAT(sqkan.kandidat_ime, ' ', sqkan.kandidat_prezime) LIKE ('%".$search_text."%')
				AND 
				(
					sqkan.kandidat_nalog_id, 
					CASE WHEN sqkan.kandidat_ppa_partner_id IS NOT NULL THEN sqkan.kandidat_ppa_partner_id ELSE 0 END
				) IN(
					SELECT
						pua.pua_nalog_id,
						CASE WHEN pua.pua_partner_id IS NOT NULL THEN pua.pua_partner_id ELSE 0 END
					FROM
						idk_pp_user_access pua
					JOIN idk_pp_users pu ON
						pu.pu_id = pua.pua_user_id
					WHERE
						pua.pua_user_id = ".$userId."
				)
			) kan
			JOIN idk_nalozi n
			ON n.nalog_id = kan.kandidat_nalog_id
			JOIN idk_companies com
			ON com.company_id = n.kompanija_id
			WHERE 
			kan.kandidat_status_prijave IN (4,10,9,8,7,12,15,18,21,24,27) 
			OR kan.kandidat_id IN (
				SELECT pk.pk_kandidatid
				FROM idk_project_kandidati pk
				JOIN (
					SELECT sqp.project_id
					FROM idk_projects sqp
					WHERE sqp.project_nalogid IN (
						SELECT
							ppua.pua_nalog_id
						FROM
							idk_pp_user_access ppua
						JOIN idk_pp_users ppu ON
							ppu.pu_id = ppua.pua_user_id
						WHERE
							ppua.pua_user_id = ".$userId."
					)
					AND sqp.project_name LIKE ('%Intervju%')
				) p 
				ON p.project_id = pk.pk_projectid
			)

			UNION

			SELECT 
				kan.kandidat_id,
				p.nalog_naziv,
				kan.kandidat_status_prijave,
				kan.kandidat_ime,
				kan.kandidat_prezime,
				kan.kandidat_check,
				p.nalog_id as kandidat_nalog_id,
				p.company_name,
				1 as is_rejected
			FROM idk_kandidati kan
			JOIN idk_project_kandidati pk
			ON pk.pk_kandidatid = kan.kandidat_id
			JOIN (
				SELECT sqp.project_id, sqn.nalog_naziv, sqn.nalog_id, sq_com.company_name
				FROM idk_projects sqp
				JOIN idk_nalozi sqn
				ON sqn.nalog_id = sqp.project_nalogid
				JOIN idk_companies sq_com
				ON sq_com.company_id = sqn.kompanija_id
				WHERE sqp.project_name LIKE ('%Odbijen%')
				AND sqp.project_nalogid IN (
					SELECT
						pua.pua_nalog_id
					FROM
						idk_pp_user_access pua
					JOIN idk_pp_users pu ON
						pu.pu_id = pua.pua_user_id
					WHERE
						pua.pua_user_id = ".$userId."
				)
			) p
			ON p.project_id = pk.pk_projectid
			WHERE 				
				CONCAT(kan.kandidat_ime, ' ', kan.kandidat_prezime) LIKE ('%".$search_text."%')
		");
		$flag_show_id = showCandidateIdR($userId);
		$query_get_search_candidates -> execute();
		if($query_get_search_candidates -> rowCount()){
			?>
			<table class = "table_found_candidates" style = "text-align:center;width:100%;border-collapse: collapse;" >
			<?php 
			if($query_get_search_candidates -> rowCount() != 1){
			?>
				<tr>
					<?php 
					if($flag_show_id)
						echo '<td style = "border-top-left-radius:10px;" class = "table_found_candidates_head">ID</td>';
					?>
					<td class = "table_found_candidates_head"><?php echo $txtArray['Ime kandiadta'][$languageUser]; ?></td>
					<td class = "table_found_candidates_head"><?php echo $txtArray['Lista'][$languageUser]; ?></td>
					<td  class = "table_found_candidates_head"><?php echo $txtArray['Nalog'][$languageUser]; ?></td>
					<?php 
					if($flag_show_id)
						echo '<td style = "border-top-right-radius:10px;" class = "table_found_candidates_head">'.$txtArray['Kompanija'][$languageUser].'</td>';
					?>
				</tr>
			<?php
			}
				
			
			$row_count = 0;
			$class_even_row 	= "";
			$status_bg_color 	= "";
			$status_name 		= "";

			while($row_get_search_candidates = $query_get_search_candidates -> fetch()){
				$row_count = $row_count + 1;
				if($row_count % 2 == 0)
					$class_even_row = "table_even_row";
				else
					$class_even_row = "";
				
				$candidate_name 		= $row_get_search_candidates['kandidat_ime'];
				$candidate_lastname 	= $row_get_search_candidates['kandidat_prezime'];
				$candidate_status 		= $row_get_search_candidates['kandidat_status_prijave'];
				$candidate_id 			= $row_get_search_candidates['kandidat_id'];
				$candidate_nalog_id 	= $row_get_search_candidates['kandidat_nalog_id'];
				$candidate_is_rejected 	= $row_get_search_candidates['is_rejected'];
				$candidate_nalog_name 	= $row_get_search_candidates['nalog_naziv'];
				$candidate_key 			= $row_get_search_candidates['kandidat_check'];
				$company_name 			= $row_get_search_candidates['company_name'];

				$candidate_status_output = "";
				if($candidate_is_rejected){
					$append_to_link = "&bar_id=5&type=1";
					$candidate_status_output = getStatusPrijaveOutput(5);

				}
				else{
					$append_to_link = "";
					$candidate_status_output = getStatusPrijaveOutput($candidate_status);
				}
				?>
					<tr class = "<?php echo $class_even_row; ?>">
						<?php 
						if($flag_show_id)
							echo '<td class = "table_found_candidates_cell">'.$candidate_id.'</td>';
						?>
						<td class = "table_found_candidates_cell"><a style = "color: #575656!important;"target="_blank" href="<?php echo getSiteUrlr(); ?>profile?&kandidat_id=<?php echo $candidate_key; ?>&n=<?php echo $candidate_nalog_id.$append_to_link; ?>"><?php echo $candidate_name." ".$candidate_lastname; ?></a></td>
						<td class = "table_found_candidates_cell"><?php echo $candidate_status_output; ?></td>
						<td class = "table_found_candidates_cell" style = "text-align:center;"><?php echo $candidate_nalog_name; ?></td>
						<?php 
						if($flag_show_id)
							echo '<td class = "table_found_candidates_cell">'.$company_name.'</td>';
						?>
					</tr>
				<?php
			}
			?>
			</table>
			<?php
		}
		else{
			echo $txtArray["Nema rezultata za: "][$languageUser].'<b><i>'.$search_text.'<b><i>';
		}
	break;

	case "load_modal_candidate_absent":
		$nalog_id 		= $_REQUEST['nalog_id'];
		$pap_id 	= $_REQUEST['pap_id'];
		$candidate_key 	= $_REQUEST['candidate_id'];
		$userIsJobStepEmployee = checkUserIsJobStepEmployee();

		?>
		
		<div class="modal fade" id="modalAbsentCandidate" aria-hidden="true" aria-labelledby="modalAbsentCandidateLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content border-0">
					<div class="modal-header border-bottom-0">
						<h5 class="modal-title w-100" id="modalAbsentCandidateLabel"><?php echo $txtArray["Uklonite kandidata iz liste"][$languageUser]; ?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body" style = "text-align:center;">
						<?php
							if ($userIsJobStepEmployee == 1) { 
								?>
									<div class="alert alert-light" role="alert">
										<?php echo $txtArray["Jeste li sigurni da želite uklonuti kandidata iz liste?"][$languageUser]; ?>
									</div>
								<?php
							} else {
								?> 
									<div class="alert alert-danger" role="alert">
										<?php echo $txtArray["Nemate privilegije za uklanjanje kandidata iz liste!"][$languageUser]; ?>
									</div>
								<?php 
							}
						?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $txtArray["Zatvori"][$languageUser]; ?></button>
						<?php
							if ($userIsJobStepEmployee == 1) { 
								?>
									<button class="btn btn-danger" id = "remove_absent_candidate" nalog_id = "<?php echo $nalog_id?>" pap_id = "<?php echo $pap_id?>" candidate_key = "<?php echo $candidate_key?>"><?php echo $txtArray["Da"][$languageUser]; ?></button>
								<?php 
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	break;

	case "remove_absent_candidate":		
		$candidate_key 	= $_REQUEST['candidate_key'];
		$nalog_id 		= $_REQUEST['nalog_id'];
		$pap_id 		= $_REQUEST['pap_id'];
		$candidate_id 	= getCandidateIdByKey($candidate_key);

		$query_get_project_id_from = $db -> prepare("
			SELECT pk_id, pk_projectid 
			FROM idk_project_kandidati pk
			JOIN idk_projects pr 
			ON pr.project_id = pk.pk_projectid
			WHERE pr.project_name LIKE ('%Intervju%')
			AND pk.pk_kandidatid = :candidate_id
			AND pr.project_nalogid = :nalog_id
		");
		
		$query_get_project_id_from -> execute(array(
			':nalog_id' => $nalog_id,
			':candidate_id' => $candidate_id
		));

		$row_get_project_id_from 	= $query_get_project_id_from -> fetch();
		$project_id_from 			= $row_get_project_id_from['pk_projectid'];
		$project_candidate_id_from 	= $row_get_project_id_from['pk_id'];
		
		$query_get_project_id_to = $db -> prepare("
			SELECT project_id
			FROM idk_projects
			WHERE project_nalogid = :nalog_id
			AND project_name LIKE ('%Nije došao na razgovor%')
		");
		
		$query_get_project_id_to -> execute(array(':nalog_id' => $nalog_id));
		
		$row_get_project_id_to = $query_get_project_id_to -> fetch();
		
		$project_id_to = $row_get_project_id_to['project_id'];
		
		$upd_status_prijave = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = 2 WHERE kandidat_id = $candidate_id");
		$upd_status_prijave->execute();
		
		$del_project = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_id = $project_candidate_id_from");
		$del_project->execute();
		
		$query = $db->prepare("
					INSERT INTO idk_project_kandidati
						(pk_projectid, pk_kandidatid)
					VALUES
						(:pk_projectid, :pk_kandidatid)");
		
		$query->execute(array(
						':pk_projectid' => $project_id_to,
						':pk_kandidatid' => $candidate_id
		));

		$log_date = date('Y-m-d H:i:s');
		addToLogsStatusPrijave($project_id_from, $project_id_to, 2, $candidate_id, 2);
		$log_desc = "Odbio kandidata [".$candidate_id."] iz naloga ".$nalog_id;
		addToLogs($log_desc);
	break;

	case "get_circle_bar_menu":
			?>
			<div class = "row" style = "text-align:center;">
				<div class = "col-4">
					<div class = "offset-2 col-8 circle_bar_menu" id = "menu_bars_contract" type = "2"><?php echo $txtArray["Ugovor dashboard"][$languageUser]; ?></div>
				</div>
				<div class = "col-4">
					<div class = "offset-2 col-8 circle_bar_menu" id = "menu_bars_diploma_certificate" type = "3"><?php echo $txtArray["Aktuelni status dashboard"][$languageUser]; ?></div>
				</div>
				<div class = "col-4">
					<div class = "offset-2 col-8 circle_bar_menu" id = "menu_bars_visa" type = "4"><?php echo $txtArray["Viza dashboard"][$languageUser]; ?></div>
				</div>
			</div>
			<?php
	break;

	case "documentDetails":
		$nrd_id = intval($_POST["nrd_id"]);
		$doc_id = intval($_POST["doc_id"]);
		$candidat_id = intval($_POST["candidat_id"]);
		$nalog_id = intval($_POST["nalog_id"]);

		?>
			<div class = "row">
				<div class="col-lg-12">
					<?php 
						$queryInfo = $db->prepare("
							SELECT 
								dt.doc_type_name, dt.doc_type_name_de, nrd.nrd_comment, d.doc_file_name, ds.ds_id, ds.ds_name_pp, ds.ds_name_de_pp, dsl.dsl_date, dsl.dsl_user_id, dsl.dsl_pp_user_id
							FROM 
								idk_pp_document_types dt
							INNER JOIN 
								idk_pp_nalog_required_documents nrd
							ON 
								dt.doc_type_id = nrd.nrd_type_id AND nrd.nrd_id = :nrd_id AND nrd.nrd_nalog_id = :nrd_nalog_id AND nrd.nrd_status = 1 AND nrd.nrd_done_by = 1
							INNER JOIN 
								idk_pp_documents d
							ON 
								nrd.nrd_id = d.doc_nrd_id AND d.doc_nrd_id = :nrd_id AND d.doc_candidate_id = :doc_candidate_id AND d.doc_id = :doc_id
							INNER JOIN 
								idk_pp_documents_statuses ds 
							ON 
								d.doc_status = ds.ds_id 
							INNER JOIN 
								idk_pp_documents_status_logs dsl
							ON 
								ds.ds_id = dsl.dsl_doc_status AND d.doc_id = dsl.dsl_doc_id AND dsl.dsl_days_count is null
						");
						$queryInfo->execute(array(
							':nrd_id' => $nrd_id,
							':doc_id' => $doc_id,
							':doc_candidate_id' => $candidat_id,
							':nrd_nalog_id' => $nalog_id
						));
						if($queryInfo->rowCount() == 1){
							$rowInfo = $queryInfo->fetch();
							$doc_name = "";
							$doc_comment = "";
							$doc_status = "";
							$doc_user ="";
							$doc_date_status = "";
							if($languageUser == 1){
								$doc_name = $rowInfo["doc_type_name_de"];
							}else{
								$doc_name = $rowInfo["doc_type_name"];
							}
							
							$nrd_comment = $rowInfo["nrd_comment"];
							if($nrd_comment != NULL){
								$doc_comment = '<textarea class="textArreaStyle form-control" rows="4" disabled>'.$nrd_comment.'</textarea> ';
							}else{
								$doc_comment = '<div class="alert alert-danger text-center" role="alert">'.$txtArray["Komentar nije napisan"][$languageUser].'!</div>';
							}
							$ds_id = intval($rowInfo["ds_id"]);
							if($languageUser == 1){
								$doc_status = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($ds_id).'>'.$rowInfo["ds_name_de_pp"].'</span>';
							}else{
								$doc_status = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($ds_id).'>'.$rowInfo["ds_name_pp"].'</span>';
							}
							$doc_date_status = date("d.m.Y", strtotime($rowInfo["dsl_date"]));
							$dsl_user_id = intval($rowInfo["dsl_user_id"]);
							$dsl_pp_user_id = intval($rowInfo["dsl_pp_user_id"]);
							if($dsl_user_id == 0 AND $dsl_pp_user_id == 0){
								$doc_user = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
							}else if($dsl_user_id != 0 AND $dsl_pp_user_id == 0){
								$doc_user = '<span class="badge bg-light text-dark rounded-pill fs-6">'.getEmployeeFullNameById($dsl_user_id).'</span>'; 
							}else if($dsl_user_id == 0 AND $dsl_pp_user_id != 0){
								$doc_user = '<span class="badge bg-light text-dark rounded-pill fs-6">'.getFirstAndLastNameUserR($dsl_pp_user_id).'</span>';
							}else{
								$doc_user = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
							}

					?>
					<style>
						.columnFixStyle{
							color: #1D84C0; 
							font-style: normal; 
							font-weight: 600; 
							font-size: 16px; 
						}
						.boxFisStyle{
							box-shadow: 0px 8px 24px rgb(112 144 176 / 15%);
							border-radius: 20px;
							padding: 20px;
						}
					</style>
					<div class=" mb-5">
						<div class = "row g-2">
							<div class="col-lg-3">
								<div class="row mb-1">
									<div class="col-md-12 columnFixStyle">
										<?php echo $txtArray["Naziv dokumenta"][$languageUser];?> 
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-12">
										<?php echo $doc_name;?>
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="row mb-1">
									<div class="col-md-12 columnFixStyle">
										<?php echo $txtArray["Status dokumenta"][$languageUser];?>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-12">
										<?php echo $doc_status;?>
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="row mb-1">
									<div class="col-md-12 columnFixStyle">
										<?php echo $txtArray["Datum"][$languageUser];?>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-12">
										<?php echo $doc_date_status;?>
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="row mb-1">
									<div class="col-md-12 columnFixStyle">
										<?php echo $txtArray["Korisnik"][$languageUser];?>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-12">
										<?php echo $doc_user;?>
									</div>
								</div>
							</div>
						</div>
						<div class = "row g-2">
							<div class="col-lg-12">
								<div class="row mb-1">
									<div class="col-md-12 columnFixStyle">
										<?php echo $txtArray["Komentar"][$languageUser];?> 
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-12">
										<?php echo $doc_comment;?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="">
						<div class = "row g-2">
							<div class="col-lg-12">
								<div class="row mb-1">
									<div class="col-md-12 columnFixStyle">
										<?php echo $txtArray["Historija statusa"][$languageUser];?> 
									</div>
								</div>
								<div class="row">
									<div class="col-md-12 scrollBarHorizontal" style="overflow-y: auto !important;">
										<table class="table table-borderless">
											<thead>
												<tr>
													<th><?php echo $txtArray["Status dokumenta"][$languageUser];?></th>
													<th><?php echo $txtArray["Datum"][$languageUser];?></th>
													<th><?php echo $txtArray["Broj dana"][$languageUser];?></th>
													<th><?php echo $txtArray["Korisnik"][$languageUser];?></th>
													<th><?php echo $txtArray["Komentar"][$languageUser];?></th>
												</tr>
											</thead>
											<tbody>
												<?php 
													$queryInfoStatusLog = $db->prepare("
														SELECT 
															dsl.dsl_doc_status, dsl.dsl_date, dsl.dsl_user_id, dsl.dsl_pp_user_id, dsl.dsl_days_count, dsl.dsl_comment, ds.ds_name_de_pp, ds.ds_name_pp
														FROM 
															idk_pp_documents_status_logs dsl
														INNER JOIN 
															idk_pp_documents_statuses ds
														ON
															dsl.dsl_doc_status = ds.ds_id
														WHERE 
															dsl.dsl_doc_id = :doc_id
														ORDER BY dsl.dsl_date ASC 
													");
													$queryInfoStatusLog->execute(array(
														":doc_id" => $doc_id
													));
													while($rowInfoStatusLog = $queryInfoStatusLog->fetch()){
														//SL - Skraćenica: Status Log
														$doc_status_SL = "";
														$doc_user_SL = "";
														$doc_status_day_count_SL = "";
														$doc_comment_SL = "";
														$dsl_doc_status_SL = intval($rowInfoStatusLog["dsl_doc_status"]);
														if($languageUser == 1){
															$doc_status_SL = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($dsl_doc_status_SL).'>'.$rowInfoStatusLog["ds_name_de_pp"].'</span>';
														}else{
															$doc_status_SL = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($dsl_doc_status_SL).'>'.$rowInfoStatusLog["ds_name_pp"].'</span>';
														}
														$dsl_date_SL = date("d.m.Y", strtotime($rowInfoStatusLog["dsl_date"]));
														$dsl_user_id_SL = $rowInfoStatusLog["dsl_user_id"];
														$dsl_pp_user_id_SL = $rowInfoStatusLog["dsl_pp_user_id"];
														if($dsl_user_id_SL == 0 AND $dsl_pp_user_id_SL == 0){
															$doc_user_SL = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
														}else if($dsl_user_id_SL != 0 AND $dsl_pp_user_id_SL == 0){
															$doc_user_SL = '<span class="badge bg-light text-dark rounded-pill fs-6">'.getEmployeeFullNameById($dsl_user_id_SL).'</span>'; 
														}else if($dsl_user_id_SL == 0 AND $dsl_pp_user_id_SL != 0){
															$doc_user_SL = '<span class="badge bg-light text-dark rounded-pill fs-6">'.getFirstAndLastNameUserR($dsl_pp_user_id_SL).'</span>';
														}else{
															$doc_user_SL = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
														}
														$dsl_days_count_SL = $rowInfoStatusLog["dsl_days_count"];
														if($dsl_days_count_SL == NULL){
															$doc_status_day_count_SL = '<span class="badge bg-light text-dark rounded-pill fs-6">'.getDaysCountR($dsl_date_SL).'</span>';
														}else{
															$doc_status_day_count_SL = '<span class="badge bg-light text-dark rounded-pill fs-6">'.$dsl_days_count_SL.'</span>';
														}
														$dsl_comment_SL = $rowInfoStatusLog["dsl_comment"];
														if($dsl_comment_SL != NULL){
															$doc_comment_SL = '<i class="fa fa-comment text-success blinkingAnimation" role="status" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$dsl_comment_SL.'" aria-hidden="true"></i>';
														}else{
															$doc_comment_SL = '<i class="fa fa-comment text-danger" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Komentar nije napisan"][$languageUser].'!" aria-hidden="true"></i>';
														}
														
														echo '
															<tr class="border-top">
																<td>'.$doc_status_SL.'</td>
																<td>'.$dsl_date_SL.'</td>
																<td>'.$doc_status_day_count_SL.'</td>
																<td>'.$doc_user_SL.'</td>
																<td>'.$doc_comment_SL.'</td>
															</tr>
														';
													}
												?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
						}
					?> 
				</div>
			</div>
		<?php

	break; 

	case "documentDetailsVisaIncomplete":
		$crd_id = intval($_POST["crd_id"]);
		$doc_id = intval($_POST["doc_id"]);
		$candidat_id = intval($_POST["candidat_id"]);
		$nalog_id = intval($_POST["nalog_id"]);
		$vi_id = intval($_POST["vi_id"]);

		?>
			<div class = "row">
				<div class="col-lg-12">
					<?php 
						$queryInfo = $db->prepare("
							SELECT 
								dt.doc_type_name, dt.doc_type_name_de, crd.crd_comment, d.doc_file_name, ds.ds_id, ds.ds_name_pp, ds.ds_name_de_pp, dsl.dsl_date, dsl.dsl_user_id, dsl.dsl_pp_user_id
							FROM 
								idk_pp_document_types dt
							INNER JOIN 
								idk_pp_cand_required_documents crd
							ON 
								dt.doc_type_id = crd.crd_type_id AND crd.crd_id = :crd_id AND crd.crd_nalog_id = :crd_nalog_id AND crd.crd_status = 1 AND crd.crd_done_by = 1 AND crd.crd_vi_id = :crd_vi_id
							INNER JOIN 
								idk_pp_documents d
							ON 
								crd.crd_id = d.doc_crd_id AND d.doc_crd_id = :crd_id AND d.doc_candidate_id = :doc_candidate_id AND d.doc_id = :doc_id
							INNER JOIN 
								idk_pp_documents_statuses ds 
							ON 
								d.doc_status = ds.ds_id 
							INNER JOIN 
								idk_pp_documents_status_logs dsl
							ON 
								ds.ds_id = dsl.dsl_doc_status AND d.doc_id = dsl.dsl_doc_id AND dsl.dsl_days_count is null
						");
						$queryInfo->execute(array(
							':crd_id' => $crd_id,
							':doc_id' => $doc_id,
							':doc_candidate_id' => $candidat_id,
							':crd_nalog_id' => $nalog_id,
							':crd_vi_id' => $vi_id
						));
						if($queryInfo->rowCount() == 1){
							$rowInfo = $queryInfo->fetch();
							$doc_name = "";
							$doc_comment = "";
							$doc_status = "";
							$doc_user ="";
							$doc_date_status = "";
							if($languageUser == 1){
								$doc_name = $rowInfo["doc_type_name_de"];
							}else{
								$doc_name = $rowInfo["doc_type_name"];
							}
							
							$crd_comment = $rowInfo["crd_comment"];
							if($crd_comment != NULL){
								$doc_comment = '<textarea class="textArreaStyle form-control" rows="4" disabled>'.$crd_comment.'</textarea> ';
							}else{
								$doc_comment = '<div class="alert alert-danger text-center" role="alert">'.$txtArray["Komentar nije napisan"][$languageUser].'!</div>';
							}
							$ds_id = intval($rowInfo["ds_id"]);
							if($languageUser == 1){
								$doc_status = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($ds_id).'>'.$rowInfo["ds_name_de_pp"].'</span>';
							}else{
								$doc_status = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($ds_id).'>'.$rowInfo["ds_name_pp"].'</span>';
							}
							$doc_date_status = date("d.m.Y", strtotime($rowInfo["dsl_date"]));
							$dsl_user_id = intval($rowInfo["dsl_user_id"]);
							$dsl_pp_user_id = intval($rowInfo["dsl_pp_user_id"]);
							if($dsl_user_id == 0 AND $dsl_pp_user_id == 0){
								$doc_user = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
							}else if($dsl_user_id != 0 AND $dsl_pp_user_id == 0){
								$doc_user = '<span class="badge bg-light text-dark rounded-pill fs-6">'.getEmployeeFullNameById($dsl_user_id).'</span>'; 
							}else if($dsl_user_id == 0 AND $dsl_pp_user_id != 0){
								$doc_user = '<span class="badge bg-light text-dark rounded-pill fs-6">'.getFirstAndLastNameUserR($dsl_pp_user_id).'</span>';
							}else{
								$doc_user = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
							}

					?>
					<style>
						.columnFixStyle{
							color: #1D84C0; 
							font-style: normal; 
							font-weight: 600; 
							font-size: 16px; 
						}
						.boxFisStyle{
							box-shadow: 0px 8px 24px rgb(112 144 176 / 15%);
							border-radius: 20px;
							padding: 20px;
						}
					</style>
					<div class=" mb-5">
						<div class = "row g-2">
							<div class="col-lg-3">
								<div class="row mb-1">
									<div class="col-md-12 columnFixStyle">
										<?php echo $txtArray["Naziv dokumenta"][$languageUser];?> 
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-12">
										<?php echo $doc_name;?>
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="row mb-1">
									<div class="col-md-12 columnFixStyle">
										<?php echo $txtArray["Status dokumenta"][$languageUser];?>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-12">
										<?php echo $doc_status;?>
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="row mb-1">
									<div class="col-md-12 columnFixStyle">
										<?php echo $txtArray["Datum"][$languageUser];?>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-12">
										<?php echo $doc_date_status;?>
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="row mb-1">
									<div class="col-md-12 columnFixStyle">
										<?php echo $txtArray["Korisnik"][$languageUser];?>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-12">
										<?php echo $doc_user;?>
									</div>
								</div>
							</div>
						</div>
						<div class = "row g-2">
							<div class="col-lg-12">
								<div class="row mb-1">
									<div class="col-md-12 columnFixStyle">
										<?php echo $txtArray["Komentar"][$languageUser];?> 
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-12">
										<?php echo $doc_comment;?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="">
						<div class = "row g-2">
							<div class="col-lg-12">
								<div class="row mb-1">
									<div class="col-md-12 columnFixStyle">
										<?php echo $txtArray["Historija statusa"][$languageUser];?> 
									</div>
								</div>
								<div class="row">
									<div class="col-md-12 scrollBarHorizontal" style="overflow-y: auto !important;">
										<table class="table table-borderless">
											<thead>
												<tr>
													<th><?php echo $txtArray["Status dokumenta"][$languageUser];?></th>
													<th><?php echo $txtArray["Datum"][$languageUser];?></th>
													<th><?php echo $txtArray["Broj dana"][$languageUser];?></th>
													<th><?php echo $txtArray["Korisnik"][$languageUser];?></th>
													<th><?php echo $txtArray["Komentar"][$languageUser];?></th>
												</tr>
											</thead>
											<tbody>
												<?php 
													$queryInfoStatusLog = $db->prepare("
														SELECT 
															dsl.dsl_doc_status, dsl.dsl_date, dsl.dsl_user_id, dsl.dsl_pp_user_id, dsl.dsl_days_count, dsl.dsl_comment, ds.ds_name_de_pp, ds.ds_name_pp
														FROM 
															idk_pp_documents_status_logs dsl
														INNER JOIN 
															idk_pp_documents_statuses ds
														ON
															dsl.dsl_doc_status = ds.ds_id
														WHERE 
															dsl.dsl_doc_id = :doc_id
														ORDER BY dsl.dsl_date ASC 
													");
													$queryInfoStatusLog->execute(array(
														":doc_id" => $doc_id
													));
													while($rowInfoStatusLog = $queryInfoStatusLog->fetch()){
														//SL - Skraćenica: Status Log
														$doc_status_SL = "";
														$doc_user_SL = "";
														$doc_status_day_count_SL = "";
														$doc_comment_SL = "";
														$dsl_doc_status_SL = intval($rowInfoStatusLog["dsl_doc_status"]);
														if($languageUser == 1){
															$doc_status_SL = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($dsl_doc_status_SL).'>'.$rowInfoStatusLog["ds_name_de_pp"].'</span>';
														}else{
															$doc_status_SL = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($dsl_doc_status_SL).'>'.$rowInfoStatusLog["ds_name_pp"].'</span>';
														}
														$dsl_date_SL = date("d.m.Y", strtotime($rowInfoStatusLog["dsl_date"]));
														$dsl_user_id_SL = $rowInfoStatusLog["dsl_user_id"];
														$dsl_pp_user_id_SL = $rowInfoStatusLog["dsl_pp_user_id"];
														if($dsl_user_id_SL == 0 AND $dsl_pp_user_id_SL == 0){
															$doc_user_SL = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
														}else if($dsl_user_id_SL != 0 AND $dsl_pp_user_id_SL == 0){
															$doc_user_SL = '<span class="badge bg-light text-dark rounded-pill fs-6">'.getEmployeeFullNameById($dsl_user_id_SL).'</span>'; 
														}else if($dsl_user_id_SL == 0 AND $dsl_pp_user_id_SL != 0){
															$doc_user_SL = '<span class="badge bg-light text-dark rounded-pill fs-6">'.getFirstAndLastNameUserR($dsl_pp_user_id_SL).'</span>';
														}else{
															$doc_user_SL = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
														}
														$dsl_days_count_SL = $rowInfoStatusLog["dsl_days_count"];
														if($dsl_days_count_SL == NULL){
															$doc_status_day_count_SL = '<span class="badge bg-light text-dark rounded-pill fs-6">'.getDaysCountR($dsl_date_SL).'</span>';
														}else{
															$doc_status_day_count_SL = '<span class="badge bg-light text-dark rounded-pill fs-6">'.$dsl_days_count_SL.'</span>';
														}
														$dsl_comment_SL = $rowInfoStatusLog["dsl_comment"];
														if($dsl_comment_SL != NULL){
															$doc_comment_SL = '<i class="fa fa-comment text-success blinkingAnimation" role="status" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$dsl_comment_SL.'" aria-hidden="true"></i>';
														}else{
															$doc_comment_SL = '<i class="fa fa-comment text-danger" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Komentar nije napisan"][$languageUser].'!" aria-hidden="true"></i>';
														}
														
														echo '
															<tr class="border-top">
																<td>'.$doc_status_SL.'</td>
																<td>'.$dsl_date_SL.'</td>
																<td>'.$doc_status_day_count_SL.'</td>
																<td>'.$doc_user_SL.'</td>
																<td>'.$doc_comment_SL.'</td>
															</tr>
														';
													}
												?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
						}
					?> 
				</div>
			</div>
		<?php
	break;
	
	case "readyToSendIdsVisaIncomplete":
		$idNalog = intval($_POST["idNalog"]);
		$idCandidate = intval($_POST["idCandidate"]);
		$idVi = intval($_POST["idVi"]);
		if($idNalog != 0 AND $idCandidate != 0){
			$query = $db->prepare("
			SELECT 
				d.doc_id, crd.crd_id, dt.doc_type_name, dt.doc_type_name_de
			FROM 
				idk_pp_documents d
			INNER JOIN 
				idk_pp_cand_required_documents crd
			ON 
				d.doc_crd_id = crd.crd_id
			INNER JOIN 
				idk_pp_document_types dt
			ON 
				crd.crd_type_id = dt.doc_type_id
			WHERE 
				d.doc_candidate_id = :candId
				AND 
				d.doc_nalog_id = :nalogId
				AND 
				d.doc_status = 9
				AND 
				d.doc_crd_id IN (
					SELECT 
						crd1.crd_id
					FROM 
						idk_pp_cand_required_documents crd1
					WHERE 
						crd1.crd_nalog_id = :nalogId
						AND 
						crd1.crd_status = 1
						AND 
						crd1.crd_done_by = 1
						AND 
						crd1.crd_vi_id = :viId
				)
			");
			$query->execute(array(
				":nalogId" => $idNalog,
				":candId" => $idCandidate,
				":viId" => $idVi
			));
			if($query->rowCount() != 0){
				$contentRow = "";
				while($row = $query->fetch()){
					$doc_name = "";
					$doc_id = intval($row["doc_id"]);
					$crd_id = intval($row["crd_id"]);
					$doc_type_name = $row["doc_type_name"];
					$doc_type_name_de = $row["doc_type_name_de"];
					if($languageUser == 1){
						$doc_name = $doc_type_name_de;
					}else{
						$doc_name = $doc_type_name;
					}
					$contentRow = $contentRow.'<label class="list-group-item"><input class="form-check-input me-2 doc_ids_SD" type="checkbox" value="'.$doc_id.'" checked disabled>'.$doc_name.'</label>';
				}

				if($contentRow != ""){
					echo '
						<div class="list-group">
							'.$contentRow.'
						</div>
					';
				}else{
					echo '';
				}
				
			}else{
				echo '';
			}
		}else{
			echo '';
		}
	break;

	case "readyToSendIds":
		$idNalog = intval($_POST["idNalog"]);
		$idCandidate = intval($_POST["idCandidate"]);
		if($idNalog != 0 AND $idCandidate != 0){

			$nacinOdlaska = getCandidateNacinOdlaska($idCandidate);
			$potpunaNostrifikacija = getCandidateFullRecognition($idCandidate);
			$uslov = "";
			if ( $nacinOdlaska == 2 OR $potpunaNostrifikacija == 1 OR $potpunaNostrifikacija == 2) {
				$uslov = "
					AND 
					nrd1.nrd_west_balkan = 1
				";
			} else if ($nacinOdlaska == 3) {
				$uslov = "
					AND 
					nrd1.nrd_work_experience = 1
				";
			} else if ($nacinOdlaska == 0) {
				$uslov = "
					AND 
					nrd1.nrd_skilled_candidates = 1
				";
			}

			$query = $db->prepare("
				SELECT 
					d.doc_id, nrd.nrd_id, dt.doc_type_name, dt.doc_type_name_de, dt.doc_type_id, d.doc_status
				FROM 
					idk_pp_documents d
				INNER JOIN 
					idk_pp_nalog_required_documents nrd
				ON 
					d.doc_nrd_id = nrd.nrd_id
				INNER JOIN 
					idk_pp_document_types dt
				ON 
					nrd.nrd_type_id = dt.doc_type_id
				WHERE 
					d.doc_candidate_id = :candId
					AND 
					d.doc_nalog_id = :nalogId
					AND 
					d.doc_status = 9
					AND 
					d.doc_nrd_id IN (
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
							".$uslov."
					)
			");
			$query->execute(array(
				":nalogId" => $idNalog,
				":candId" => $idCandidate
			));
			if($query->rowCount() != 0){
				$contentRow = "";
				while($row = $query->fetch()){
					$doc_name = "";
					$doc_id = intval($row["doc_id"]);
					$nrd_id = intval($row["nrd_id"]);
					$doc_type_name = $row["doc_type_name"];
					$doc_type_name_de = $row["doc_type_name_de"];
					$doc_type_id = $row["doc_type_id"];
					$doc_status = $row["doc_status"];
					if ($doc_type_id == 1 AND $doc_status != 9){
						continue;
					}
					if($languageUser == 1){
						$doc_name = $doc_type_name_de;
					}else{
						$doc_name = $doc_type_name;
					}
					$contentRow = $contentRow.'<label class="list-group-item"><input class="form-check-input me-2 doc_ids_SD" type="checkbox" value="'.$doc_id.'" checked disabled>'.$doc_name.'</label>';
				}

				if($contentRow != ""){
					echo '
						<div class="list-group">
							'.$contentRow.'
						</div>
					';
				}else{
					echo '';
				}
				
			}else{
				echo '';
			}
		}else{
			echo '';
		}
	break;

	case "readyToSendVisaIncomplete":
		$idNalog = intval($_POST["idNalog"]);
		$idCandidate = intval($_POST["idCandidate"]);
		$viId = intval($_POST["viId"]);
		if($idNalog != 0 AND $idCandidate != 0){
			$query = $db->prepare("
				SELECT
					(
						SELECT 
							count(crd.crd_id) AS countCrd
						FROM 
							idk_pp_cand_required_documents crd
						WHERE 
							crd.crd_nalog_id = :nalogId
							AND 
							crd.crd_status = 1
							AND 
							crd.crd_done_by = 1
							AND 
							crd.crd_vi_id = :viId
					) AS numberRequired, 
					(
						SELECT 
							count(d.doc_id) AS countDoc
						FROM 
							idk_pp_documents d
						WHERE 
							d.doc_candidate_id = :candId
							AND 
							d.doc_nalog_id = :nalogId
							AND 
							d.doc_status = 9
							AND 
							d.doc_crd_id IN (
								SELECT 
									crd1.crd_id
								FROM 
									idk_pp_cand_required_documents crd1
								WHERE 
									crd1.crd_nalog_id = :nalogId
									AND 
									crd1.crd_status = 1
									AND 
									crd1.crd_done_by = 1
									AND 
									crd1.crd_vi_id = :viId
							)
					) AS numberReadyToSend
			");
			$query->execute(array(
				':nalogId' => $idNalog, 
				':candId' => $idCandidate,
				':viId' => $viId
			));
			$row = $query->fetch();
			$numberRequired = intval($row["numberRequired"]);
			$numberReadyToSend = intval($row["numberReadyToSend"]);
			if($numberRequired == $numberReadyToSend AND $numberRequired != 0 AND $numberReadyToSend != 0){
				echo '
					<button 
						type="button"  
						class="btn btn-info blinkingAnimation"
						data-bs-toggle="modal"
						data-bs-target="#sendDocumentsVI"
						onclick="sendDocumentsInfoVI(this)"
						data-nalog_id = "'.$idNalog.'"
						data-candidate_id = "'.$idCandidate.'"
						data-vi_id = "'.$viId.'"
					>
						<i class="fa fa-paper-plane me-2" aria-hidden="true"></i>
						'.$txtArray["Pošalji dokumente"][$languageUser].'
					</button>
				';
			}else{
				echo '';
			}
		}else{
			echo '';
		}
	break;

	case "readyToSend":
		$idNalog = intval($_POST["idNalog"]);
		$idCandidate = intval($_POST["idCandidate"]);
		if($idNalog != 0 AND $idCandidate != 0){
			$idPartner = getPartnerForCandidateR($idCandidate);

			$nacinOdlaska = getCandidateNacinOdlaska($idCandidate);
			$potpunaNostrifikacija = getCandidateFullRecognition($idCandidate);
			$uslov = "";
			if ( $nacinOdlaska == 2 OR $potpunaNostrifikacija == 1 OR $potpunaNostrifikacija == 2) {
				$uslov1 = "
					AND 
					nrd.nrd_west_balkan = 1
				";
				$uslov2 = "
					AND 
					nrd1.nrd_west_balkan = 1
				";
			} else if ($nacinOdlaska == 3) {
				$uslov1 = "
					AND 
					nrd.nrd_work_experience = 1
				";
				$uslov2 = "
					AND 
					nrd1.nrd_work_experience = 1
				";
			} else if ($nacinOdlaska == 0) {
				$uslov1 = "
					AND 
					nrd.nrd_skilled_candidates = 1
				";
				$uslov2 = "
					AND 
					nrd1.nrd_skilled_candidates = 1
				";
			}
			/*
				Ovdje radimo provjeru da bi vidjeli da li treba brojati ugovor ili ne. 
				To je urađeno iz razloga što je kod nekog kandidata ugovor na statusu Spreman
				A u slučaju devalidacije ugovora - može se desiti da je ugovor na statusu Spreman za slanje
				START
			 	*/
					$queryCheckContract = $db->prepare("
						SELECT 
							count(ppd.doc_id) AS countContract
						FROM 
							idk_pp_documents ppd
						WHERE 
							ppd.doc_candidate_id = :candId
							AND 
							ppd.doc_nalog_id = :nalogId
							AND 
							ppd.doc_status = 18 
							AND 
							ppd.doc_nrd_id IN (
								SELECT 
									nrd.nrd_id
								FROM 
									idk_pp_nalog_required_documents nrd
								WHERE 
									nrd.nrd_nalog_id = :nalogId
									AND 
									nrd.nrd_status = 1
									AND 
									nrd.nrd_done_by = 1
									AND 
									nrd.nrd_type_id = 1
									".$uslov1."
							)
					");
					$queryCheckContract->execute(array(
						':nalogId' => $idNalog, 
						':candId' => $idCandidate
					));
					$rowCheckContract = $queryCheckContract->fetch();
					$countContract = $rowCheckContract["countContract"]; 

					$uslovUgovor1 = ""; 
					$uslovUgovor2 = ""; 
					if ($countContract == 1) {
						$uslovUgovor1 = " 
							AND 
							nrd.nrd_type_id != 1
						";
						$uslovUgovor2 = "
							AND 
							nrd1.nrd_type_id != 1
						";
					}
				/*
				END
			*/

			$query = $db->prepare("
				SELECT
					(
						SELECT 
							count(nrd.nrd_id) AS countNrd
						FROM 
							idk_pp_nalog_required_documents nrd
						WHERE 
							nrd.nrd_nalog_id = :nalogId
							AND 
							nrd.nrd_status = 1
							AND 
							nrd.nrd_done_by = 1
							".$uslovUgovor1."
							".$uslov1."
					) AS numberRequired, 
					(
						SELECT 
							count(d.doc_id) AS countDoc
						FROM 
							idk_pp_documents d
						WHERE 
							d.doc_candidate_id = :candId
							AND 
							d.doc_nalog_id = :nalogId
							AND 
							d.doc_status = 9
							AND 
							d.doc_nrd_id IN (
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
									".$uslovUgovor2."
									".$uslov2."
							)
					) AS numberReadyToSend
			");
			$query->execute(array(
				':nalogId' => $idNalog, 
				':candId' => $idCandidate
			));
			$row = $query->fetch();
			$numberRequired = intval($row["numberRequired"]);
			$numberReadyToSend = intval($row["numberReadyToSend"]);
			if($numberRequired == $numberReadyToSend AND $numberRequired != 0 AND $numberReadyToSend != 0){
				echo '
					<button 
						type="button"  
						class="btn btn-info blinkingAnimation"
						onclick="sendDocumentsInfo(this)"
						data-nalog_id = "'.$idNalog.'"
						data-candidate_id = "'.$idCandidate.'"
						data-partner_id = "'.$idPartner.'"
					>
						<i class="fa fa-paper-plane me-2" aria-hidden="true"></i>
						'.$txtArray["Pošalji dokumente"][$languageUser].'
					</button>
				';
			}else{
				echo '';
			}
		}else{
			echo '';
		}
	break;
	
	case "visaIncompleteDocuments":
		$idCan = intval($_POST["idCandidate"]);
		$idNalog = intval($_POST["idNalog"]);
		$viId = intval($_POST["viId"]);
		if($idCan != 0 AND $idNalog != 0 AND $viId != 0){
			$rows = "";
			$queryRD = $db->prepare(" 
				SELECT 
					dt.doc_type_id, dt.doc_type_name, dt.doc_type_name_de, crd.crd_id, crd.crd_comment, d.doc_id, d.doc_file_name, d.doc_status, ds.ds_name_pp, ds.ds_name_de_pp, dsl.dsl_date, dsl.dsl_user_id, dsl.dsl_pp_user_id, dsl.dsl_comment
				FROM 
					idk_pp_document_types dt
				INNER JOIN 
					idk_pp_cand_required_documents crd
				ON 
					dt.doc_type_id = crd.crd_type_id AND crd.crd_done_by = 1 AND crd.crd_nalog_id = :nalogId AND crd.crd_status = 1 AND crd.crd_vi_id = :viId
				LEFT JOIN 
					idk_pp_documents d 
				ON 
					d.doc_crd_id = crd.crd_id AND d.doc_candidate_id = :candidateId AND d.doc_nrd_id is null AND d.doc_status != 27
				LEFT JOIN 
					idk_pp_documents_statuses ds 
				ON 
					ds.ds_id = d.doc_status
				LEFT JOIN 
					idk_pp_documents_status_logs dsl
				ON 
					dsl.dsl_doc_id = d.doc_id AND dsl.dsl_days_count is null
				ORDER BY 
					crd.crd_id 
				ASC
			");
			$queryRD->execute(array(
				':nalogId' => $idNalog,
				':candidateId' => $idCan,
				':viId' => $viId
			));
			if($queryRD->rowCount() != 0){
				$crm_url = getCRMUrlr();
				while($rowRD = $queryRD->fetch()){
					$action = "";
					$details = "";
					$dsl_user = "";
					$doc_file = "";
					$commentCRD = "";
					//START
					$doc_type_id = intval($rowRD["doc_type_id"]);
					if($languageUser == 1){
						$doc_type_name = $rowRD["doc_type_name_de"];
					}else{
						$doc_type_name = $rowRD["doc_type_name"];
					}
					$crd_id = intval($rowRD["crd_id"]);
					$crd_comment = $rowRD["crd_comment"];
					if($crd_comment != NULL){
						$commentCRD = '<i class="fa fa-comment text-success blinkingAnimation fa-2x" role="status" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$crd_comment.'!" aria-hidden="true"></i>';
					}else{
						$commentCRD = '<i class="fa fa-comment text-danger fa-2x" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Komentar nije napisan"][$languageUser].'!" aria-hidden="true"></i>';
					}
					$doc_id = intval($rowRD["doc_id"]); 
					$doc_file_name = $rowRD["doc_file_name"];
					if($doc_file_name != NULL){
						$doc_file = '<a href="'.$crm_url.$doc_file_name.'" target="_blank"><button type="button" class="btn btn-success"><i class="fa fa-file" aria-hidden="true"></i></button></a>';
					}else{
						$doc_file = '<button type="button" class="btn btn-danger" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Dokument nije uploadan"][$languageUser].'"><i class="fa fa-file" aria-hidden="true"></button>';
					}
					$doc_status = intval($rowRD["doc_status"]);
					if($languageUser == 1){
						$ds_name = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($doc_status).'>'.$rowRD["ds_name_de_pp"].'</span>';
					}else{
						$ds_name = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($doc_status).'>'.$rowRD["ds_name_pp"].'</span>';
					}
					$dsl_date = date("d.m.Y", strtotime($rowRD["dsl_date"]));
					$dsl_user_id = intval($rowRD["dsl_user_id"]);
					$dsl_pp_user_id = intval($rowRD["dsl_pp_user_id"]);
					if($dsl_user_id == 0 AND $dsl_pp_user_id == 0){
						$dsl_user = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
					}else if($dsl_user_id != 0 AND $dsl_pp_user_id == 0){
						$dsl_user = '<i class="fa fa-user-circle fa-2x" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.getEmployeeFullNameById($dsl_user_id).'" aria-hidden="true"></i>';
					}else if($dsl_user_id == 0 AND $dsl_pp_user_id != 0){
						$dsl_user = '<i class="fa fa-user-circle fa-2x" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.getFirstAndLastNameUserR($dsl_pp_user_id).'" aria-hidden="true"></i>';
					}else{
						$dsl_user = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
					} 
					$dsl_comment = $rowRD["dsl_comment"];

					$details = '
						<button 
							type="button" 
							class="btn btn-info" 
							onclick="documentDetailsVI(this)" 
							data-bs-toggle="modal"
							data-bs-target="#documentDetailsVI" 
							data-crd_id = "'.$crd_id.'"
							data-candidat_id = "'.$idCan.'"
							data-nalog_id = "'.$idNalog.'"
							data-doc_id = "'.$doc_id.'"
							data-vi_id = "'.$viId.'"
						>
							<i class="fa fa-info-circle " aria-hidden="true"></i>
						</button>
					';
					//END

					if($doc_status == 0){
						$action = '
							<button 
								type="button" 
								class="btn btn-danger documentAddData blinkingAnimation" role="status"
								onclick="documentAddModalSetVI(this)" 
								data-bs-toggle="modal"
								data-bs-target="#documentAddVI" 
								data-doc_type_id="'.$doc_type_id.'"
								data-crd_id = "'.$crd_id.'"
								data-candidat_id = "'.$idCan.'"
								data-nalog_id = "'.$idNalog.'"
								data-vi_id = "'.$viId.'"
							>
								<i class="fa fa-upload" aria-hidden="true"></i>
							</button>
						';
					}else if($doc_status == 3){
						$action = '
							<button 
								type="button" 
								class="btn btn-warning"
								data-bs-container="body" 
								data-bs-toggle="popover"
								data-bs-trigger="hover focus" 
								data-bs-placement="top" 
								data-bs-content="'.$txtArray["Čeka se provjera dokumenta"][$languageUser].'"
							>
								<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
							</button>
						';
					}else if($doc_status == 6){
						$action = '
							<button 
								type="button" 
								class="btn btn-warning"
								data-bs-container="body" 
								data-bs-toggle="popover"
								data-bs-trigger="hover focus" 
								data-bs-placement="top" 
								data-bs-content="'.$txtArray["Čeka se provjera svih dokumenata nakon čega će biti omogućena opcija označavanja da su orginalni dokumenti poslani!"][$languageUser].'"
							>
								<i class="fa fa-clock-o" aria-hidden="true"></i>
							</button>
						';
					}else if($doc_status == 9){
						$action = '
							<button 
								type="button" 
								class="btn btn-warning"
								data-bs-container="body" 
								data-bs-toggle="popover"
								data-bs-trigger="hover focus" 
								data-bs-placement="top" 
								data-bs-content="'.$txtArray["Koristite opciju iznad tabele za označavanje da su dokumenti poslani!"][$languageUser].'"
							>
								<i class="fa fa-paper-plane" aria-hidden="true"></i>
							</button>
						';
					}else if($doc_status == 12){
						$action = '
							<button 
								type="button" 
								class="btn btn-warning"
								data-bs-container="body" 
								data-bs-toggle="popover"
								data-bs-trigger="hover focus" 
								data-bs-placement="top" 
								data-bs-content="'.$txtArray["Čeka se prijem dokumenata!"][$languageUser].'"
							>
								<i class="fa fa-envelope-open" aria-hidden="true"></i>
							</button>
						';
					}else if($doc_status == 21){
						$action = '
							<button 
								type="button" 
								class="btn btn-danger documentAddData blinkingAnimation" role="status"
								onclick="newDocumentAddModalSetVI(this)"
								data-bs-toggle="modal"
								data-bs-target="#newDocumentAddVI" 
								data-doc_type_id="'.$doc_type_id.'"
								data-crd_id = "'.$crd_id.'"
								data-candidat_id = "'.$idCan.'"
								data-nalog_id = "'.$idNalog.'"
								data-vi_id = "'.$viId.'"
							>
								<i class="fa fa-refresh" aria-hidden="true"></i>
							</button>
						';
					}else{
						$action = '
							<button 
								type="button" 
								class="btn btn-warning"
								data-bs-container="body" 
								data-bs-toggle="popover"
								data-bs-trigger="hover focus" 
								data-bs-placement="top" 
								data-bs-content="'.$txtArray["Na trenutnom statusu dokumenta nema predviđenih akcija."][$languageUser].'"
							>
								<i class="fa fa-times-circle" aria-hidden="true"></i>
							</button>
						';
					}
					//Ispis
					if($doc_status == 0){
						$rows = $rows . '
							<tr>
								<td class="text-center">'.$doc_type_name.'</td>
								<td colspan="4" class="text-center"><span class="badge rounded-pill bg-danger fs-5">'.$txtArray["Dokument nije uploadan"][$languageUser].'</span></td>
								<td style="display: none;"></td>
								<td style="display: none;"></td>
								<td style="display: none;"></td>
								<td class="text-center">'.$action.'</td>
								<td class="text-center">'.$commentCRD.'</td>
							</tr>
						';
					}else{
						$rows = $rows . '
							<tr>
								<td class="text-center">'.$doc_type_name.'</td>
								<td class="text-center">'.$doc_file.'</td>
								<td class="text-center">'.$ds_name.'</td>
								<td class="text-center">'.$dsl_date.'</td>
								<td class="text-center">'.$dsl_user.'</td>
								<td class="text-center">'.$action.'</td>
								<td class="text-center">'.$details.'</td>
							</tr>
						';
					}
					//Ispis
				}
				echo '
					<thead>
						<tr>
							<th class="text-center">'.$txtArray["Naziv dokumenta"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Pregled dokumenta"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Status dokumenta"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Datum"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Korisnik"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Akcija"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Detalji"][$languageUser].'</th>
						</tr>
					</thead>
					<tbody>
						'.$rows.'
					</tbody>
				';
			}else{
				echo '
					<thead>
						<tr>
							<th class="text-center">'.$txtArray["Naziv dokumenta"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Pregled dokumenta"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Status dokumenta"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Datum"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Korisnik"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Akcija"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Detalji"][$languageUser].'</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				';
			}
		}else{
			echo '
				<thead>
					<tr>
						<th class="text-center">'.$txtArray["Naziv dokumenta"][$languageUser].'</th>
						<th class="text-center">'.$txtArray["Pregled dokumenta"][$languageUser].'</th>
						<th class="text-center">'.$txtArray["Status dokumenta"][$languageUser].'</th>
						<th class="text-center">'.$txtArray["Datum"][$languageUser].'</th>
						<th class="text-center">'.$txtArray["Korisnik"][$languageUser].'</th>
						<th class="text-center">'.$txtArray["Akcija"][$languageUser].'</th>
						<th class="text-center">'.$txtArray["Detalji"][$languageUser].'</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			';
		}
	break;

	case "requiredDocuments":
		$idCan = intval($_POST["idCandidate"]);
		$idNalog = intval($_POST["idNalog"]);
		if($idCan != 0 AND $idNalog != 0){
			$idPartner = getPartnerForCandidateR($idCan);
			$idCanKey = getCandidateKeyById($idCan);

			$nacinOdlaska = getCandidateNacinOdlaska($idCan);
			$potpunaNostrifikacija = getCandidateFullRecognition($idCan);
			$uslov = "";
			if ( $nacinOdlaska == 2 OR $potpunaNostrifikacija == 1 OR $potpunaNostrifikacija == 2) {
				$uslov = " AND nrd.nrd_west_balkan = 1 ";
			} else if ($nacinOdlaska == 3) {
				$uslov = " AND nrd.nrd_work_experience = 1 ";
			} else if ($nacinOdlaska == 0) {
				$uslov = " AND nrd.nrd_skilled_candidates = 1 ";
			}

			$rows = "";
			//RD - requered documents
			$queryRD = $db->prepare("
				SELECT 
					dt.doc_type_id, dt.doc_type_name, dt.doc_type_name_de, nrd.nrd_id, nrd.nrd_comment, d.doc_id, d.doc_file_name, d.doc_status, ds.ds_name_pp, ds.ds_name_de_pp, dsl.dsl_date, dsl.dsl_user_id, dsl.dsl_pp_user_id, dsl.dsl_comment
				FROM 
					idk_pp_document_types dt
				INNER JOIN 
					idk_pp_nalog_required_documents nrd
				ON 
					dt.doc_type_id = nrd.nrd_type_id AND nrd.nrd_done_by = 1 AND nrd.nrd_nalog_id = :nalogId AND nrd.nrd_status = 1 ".$uslov."
				LEFT JOIN 
					idk_pp_documents d 
				ON 
					d.doc_nrd_id = nrd.nrd_id AND d.doc_candidate_id = :candidateId AND d.doc_crd_id is null AND d.doc_status != 27
				LEFT JOIN 
					idk_pp_documents_statuses ds 
				ON 
					ds.ds_id = d.doc_status
				LEFT JOIN 
					idk_pp_documents_status_logs dsl
				ON 
					dsl.dsl_doc_id = d.doc_id AND dsl.dsl_days_count is null
				ORDER BY 
					nrd.nrd_id 
				ASC
			");
			$queryRD->execute(array(
				':nalogId' => $idNalog,
				':candidateId' => $idCan
			));
			if($queryRD->rowCount() != 0){
				$crm_url = getCRMUrlr();
				while($rowRD = $queryRD->fetch()){
					$action = "";
					$details = "";
					$dsl_user = "";
					$doc_file = "";
					$commentNRD = "";
					//START
					$doc_type_id = intval($rowRD["doc_type_id"]);
					if($languageUser == 1){
						$doc_type_name = $rowRD["doc_type_name_de"];
					}else{
						$doc_type_name = $rowRD["doc_type_name"];
					}
					$nrd_id = intval($rowRD["nrd_id"]);
					$nrd_comment = $rowRD["nrd_comment"];
					if($nrd_comment != NULL){
						$commentNRD = '<i class="fa fa-comment text-success blinkingAnimation fa-2x" role="status" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$nrd_comment.'!" aria-hidden="true"></i>';
					}else{
						$commentNRD = '<i class="fa fa-comment text-danger fa-2x" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Komentar nije napisan"][$languageUser].'!" aria-hidden="true"></i>';
					}
					$doc_id = intval($rowRD["doc_id"]); 
					$doc_file_name = $rowRD["doc_file_name"];
					if($doc_file_name != NULL){
						
						$doc_file = '<a href="'.$crm_url.$doc_file_name.'" target="_blank"><button type="button" class="btn btn-success"><i class="fa fa-file" aria-hidden="true"></i></button></a>';
					}else{
						$doc_file = '<button type="button" class="btn btn-danger" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Dokument nije uploadan"][$languageUser].'"><i class="fa fa-file" aria-hidden="true"></button>';
					}
					$doc_status = intval($rowRD["doc_status"]);
					if($languageUser == 1){
						$ds_name = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($doc_status).'>'.$rowRD["ds_name_de_pp"].'</span>';
					}else{
						$ds_name = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($doc_status).'>'.$rowRD["ds_name_pp"].'</span>';
					}
					$dsl_date = date("d.m.Y", strtotime($rowRD["dsl_date"]));
					$dsl_user_id = intval($rowRD["dsl_user_id"]);
					$dsl_pp_user_id = intval($rowRD["dsl_pp_user_id"]);
					if($dsl_user_id == 0 AND $dsl_pp_user_id == 0){
						$dsl_user = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
					}else if($dsl_user_id != 0 AND $dsl_pp_user_id == 0){
						$dsl_user = '<i class="fa fa-user-circle fa-2x" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.getEmployeeFullNameById($dsl_user_id).'" aria-hidden="true"></i>';
					}else if($dsl_user_id == 0 AND $dsl_pp_user_id != 0){
						$dsl_user = '<i class="fa fa-user-circle fa-2x" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.getFirstAndLastNameUserR($dsl_pp_user_id).'" aria-hidden="true"></i>';
					}else{
						$dsl_user = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
					} 
					$dsl_comment = $rowRD["dsl_comment"];

					$details = '
						<button 
							type="button" 
							class="btn btn-info" 
							onclick="documentDetails(this)" 
							data-bs-toggle="modal"
							data-bs-target="#documentDetails" 
							data-nrd_id = "'.$nrd_id.'"
							data-candidat_id = "'.$idCan.'"
							data-nalog_id = "'.$idNalog.'"
							data-doc_id = "'.$doc_id.'" 
						>
							<i class="fa fa-info-circle " aria-hidden="true"></i>
						</button>
					';
					//END

					if($doc_status == 0){
						$action = '
							<button 
								type="button" 
								class="btn btn-danger documentAddData blinkingAnimation" role="status"
								onclick="documentAddModalSet(this)"
								data-doc_type_id="'.$doc_type_id.'"
								data-nrd_id = "'.$nrd_id.'"
								data-candidat_id = "'.$idCan.'"
								data-nalog_id = "'.$idNalog.'"
								data-candidat_key = "'.$idCanKey.'"
								data-candidat_partner_id = "'.$idPartner.'"
							>
								<i class="fa fa-upload" aria-hidden="true"></i>
							</button>
						';
					}else if($doc_status == 3){
						$action = '
							<button 
								type="button" 
								class="btn btn-warning"
								data-bs-container="body" 
								data-bs-toggle="popover"
								data-bs-trigger="hover focus" 
								data-bs-placement="top" 
								data-bs-content="'.$txtArray["Čeka se provjera dokumenta"][$languageUser].'"
							>
								<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
							</button>
						';
					}else if($doc_status == 6){
						$action = '
							<button 
								type="button" 
								class="btn btn-warning"
								data-bs-container="body" 
								data-bs-toggle="popover"
								data-bs-trigger="hover focus" 
								data-bs-placement="top" 
								data-bs-content="'.$txtArray["Čeka se provjera svih dokumenata nakon čega će biti omogućena opcija označavanja da su orginalni dokumenti poslani!"][$languageUser].'"
							>
								<i class="fa fa-clock-o" aria-hidden="true"></i>
							</button>
						';
					}else if($doc_status == 9){
						$action = '
							<button 
								type="button" 
								class="btn btn-warning"
								data-bs-container="body" 
								data-bs-toggle="popover"
								data-bs-trigger="hover focus" 
								data-bs-placement="top" 
								data-bs-content="'.$txtArray["Koristite opciju iznad tabele za označavanje da su dokumenti poslani!"][$languageUser].'"
							>
								<i class="fa fa-paper-plane" aria-hidden="true"></i>
							</button>
						';
					}else if($doc_status == 12){
						$action = '
							<button 
								type="button" 
								class="btn btn-warning"
								data-bs-container="body" 
								data-bs-toggle="popover"
								data-bs-trigger="hover focus" 
								data-bs-placement="top" 
								data-bs-content="'.$txtArray["Čeka se prijem dokumenata!"][$languageUser].'"
							>
								<i class="fa fa-envelope-open" aria-hidden="true"></i>
							</button>
						';
					}else if($doc_status == 21){
						$action = '
							<button 
								type="button" 
								class="btn btn-danger documentAddData blinkingAnimation" role="status"
								onclick="newDocumentAddModalSet(this)"
								data-bs-toggle="modal"
								data-bs-target="#newDocumentAdd" 
								data-doc_type_id="'.$doc_type_id.'"
								data-nrd_id = "'.$nrd_id.'"
								data-candidat_id = "'.$idCan.'"
								data-nalog_id = "'.$idNalog.'"
								data-candidate_key = "'.$idCanKey.'"
								data-candidat_partner_id = "'.$idPartner.'"
							>
								<i class="fa fa-refresh" aria-hidden="true"></i>
							</button>
						';
					}else{
						$action = '
							<button 
								type="button" 
								class="btn btn-warning"
								data-bs-container="body" 
								data-bs-toggle="popover"
								data-bs-trigger="hover focus" 
								data-bs-placement="top" 
								data-bs-content="'.$txtArray["Na trenutnom statusu dokumenta nema predviđenih akcija."][$languageUser].'"
							>
								<i class="fa fa-times-circle" aria-hidden="true"></i>
							</button>
						';
					}
					//Ispis
					if($doc_status == 0){
						$rows = $rows . '
							<tr>
								<td class="text-center">'.$doc_type_name.'</td>
								<td colspan="4" class="text-center"><span class="badge rounded-pill bg-danger fs-5">'.$txtArray["Dokument nije uploadan"][$languageUser].'</span></td>
								<td style="display: none;"></td>
								<td style="display: none;"></td>
								<td style="display: none;"></td>
								<td class="text-center">'.$action.'</td>
								<td class="text-center">'.$commentNRD.'</td>
							</tr>
						';
					}else{
						$rows = $rows . '
							<tr>
								<td class="text-center">'.$doc_type_name.'</td>
								<td class="text-center">'.$doc_file.'</td>
								<td class="text-center">'.$ds_name.'</td>
								<td class="text-center">'.$dsl_date.'</td>
								<td class="text-center">'.$dsl_user.'</td>
								<td class="text-center">'.$action.'</td>
								<td class="text-center">'.$details.'</td>
							</tr>
						';
					}
					//Ispis
				}
				echo '
					<thead>
						<tr>
							<th class="text-center">'.$txtArray["Naziv dokumenta"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Pregled dokumenta"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Status dokumenta"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Datum"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Korisnik"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Akcija"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Detalji"][$languageUser].'</th>
						</tr>
					</thead>
					<tbody>
						'.$rows.'
					</tbody>
				';
			}else{
				echo '
					<thead>
						<tr>
							<th class="text-center">'.$txtArray["Naziv dokumenta"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Pregled dokumenta"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Status dokumenta"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Datum"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Korisnik"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Akcija"][$languageUser].'</th>
							<th class="text-center">'.$txtArray["Detalji"][$languageUser].'</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				';
			}
		}else{
			echo '
				<thead>
					<tr>
						<th class="text-center">'.$txtArray["Naziv dokumenta"][$languageUser].'</th>
						<th class="text-center">'.$txtArray["Pregled dokumenta"][$languageUser].'</th>
						<th class="text-center">'.$txtArray["Status dokumenta"][$languageUser].'</th>
						<th class="text-center">'.$txtArray["Datum"][$languageUser].'</th>
						<th class="text-center">'.$txtArray["Korisnik"][$languageUser].'</th>
						<th class="text-center">'.$txtArray["Akcija"][$languageUser].'</th>
						<th class="text-center">'.$txtArray["Detalji"][$languageUser].'</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			';
		}
	break;
	case "get_forecast_calendar":

		include_once ('includes/classes/candidatesProjection.php');
		include_once ('includes/classes/forecastCalendar.php');
		$durationPerStatus = new durationPerStatus();

		$selected_year = $_REQUEST['selected_year'];

		$candidatesProjection   = new candidatesProjection($durationPerStatus, NULL, TRUE);
		$nalog_data = array();
		$userAccess = getUserAccess($userId);
		$nalog_ids = $userAccess['superAdminAccess']['nalog'];

		
		foreach($nalog_ids as $nalog_id){
			array_push($nalog_data, new nalogData($candidatesProjection -> getCandidateProjectionRows(), $nalog_id, $selected_year));
		}
		
		if($selected_year == 0){
			
			$max_year_to_check = "";
			$min_year_to_check = "";
			
			
			$min_year = $nalog_data[0] -> getNalogMinYear();
			$max_year = $nalog_data[0] -> getNalogMaxYear();
			
			for($i = 1; $i < count($nalog_data); $i++){
				$max_year_to_check = $nalog_data[$i] -> getNalogMaxYear();
				$min_year_to_check = $nalog_data[$i] -> getNalogMinYear();
			
				if($min_year > $min_year_to_check){
					$min_year = $min_year_to_check;
				}
				if($max_year < $max_year_to_check){
					$max_year = $max_year_to_check;
				}
			}
			if($max_year == 1000){
				$max_year = date("Y");
			}
			if($min_year == 3000){
				$min_year = date("Y");
			}
		
			$nalog_data[0] -> min_year = $min_year;
			$nalog_data[0] -> max_year = $max_year;

			$nalog_data[0] -> select_year ='<img class = "year_change" id = "forecast_year_left" min = "'.$min_year.'"src = "images/arrow_left.png"><span class = "selected_forecast_year" id = "selected_forecast_year">'.$min_year.'</span><img  class = "year_change"  id = "forecast_year_right" max="'.$max_year.'" src = "images/arrow_right.png">';
		}
		
		$nalog_data[0] -> table ='
			<table id="table_forecast" class="hover striped row-border" cellspacing="0" width="96%" style = "margin-left:2%">
				<thead>
					<tr>
						<th class="text-left" style = "font-size: 16px;">'.$txtArray["Nalog"][$languageUser].'</th>
						<th class="text-left" style = "font-size: 16px;">Jan</th>								
						<th class="text-left" style = "font-size: 16px;"">Feb</th>	
						<th class="text-left" style = "font-size: 16px;"">Mar</th>	
						<th class="text-left" style = "font-size: 16px;"">Apr</th>	
						<th class="text-left" style = "font-size: 16px;"">May</th>	
						<th class="text-left" style = "font-size: 16px;"">Jun</th>	
						<th class="text-left" style = "font-size: 16px;"">Jul</th>	
						<th class="text-left" style = "font-size: 16px;"">Aug</th>	
						<th class="text-left" style = "font-size: 16px;"">Sep</th>	
						<th class="text-left" style = "font-size: 16px;"">Oct</th>	
						<th class="text-left" style = "font-size: 16px;"">Nov</th>	
						<th class="text-left" style = "font-size: 16px;"">Dec</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
					<tr id = "forecast_calendar_append_footer_cells">
						
					</tr>
				</tfoot>
			</table>
		';
		echo json_encode($nalog_data);
	break;

	case "get_forecast_list":
		// include('includes/classes/candidatesProjection.php');
		$nalog_id = $_REQUEST['nalog_id'];
		$month = $_REQUEST['month'];
		$year = $_REQUEST['year'];
		$partner_id = $_REQUEST['partner_id'];
		$month_text = "";
		$pre_month_text = "";
		if($nalog_id == 0){
			$pre_month_text = "";
		}
		else if($partner_id == 0){
			$pre_month_text = getNazivNalogaR($nalog_id).", ";
		}
		else{
			$pre_month_text = getCompanyNameR($partner_id).", ";
		}
		switch($month){
			case 1:
				$month_text = $pre_month_text.$txtArray["Januar"][$languageUser]." ".$year;
			break;
			case 2:
				$month_text = $pre_month_text.$txtArray["Februar"][$languageUser]." ".$year;
			break;
			case 3:
				$month_text = $pre_month_text.$txtArray["Mart"][$languageUser]." ".$year;
			break;
			case 4:
				$month_text = $pre_month_text.$txtArray["April"][$languageUser]." ".$year;
			break;
			case 5:
				$month_text = $pre_month_text.$txtArray["Maj"][$languageUser]." ".$year;
			break;
			case 6:
				$month_text = $pre_month_text.$txtArray["Juni"][$languageUser]." ".$year;
			break;
			case 7:
				$month_text = $pre_month_text.$txtArray["Juli"][$languageUser]." ".$year;
			break;
			case 8:
				$month_text = $pre_month_text.$txtArray["August"][$languageUser]." ".$year;
			break;
			case 9:
				$month_text = $pre_month_text.$txtArray["Septembar"][$languageUser]." ".$year;
			break;
			case 10:
				$month_text = $pre_month_text.$txtArray["Oktobar"][$languageUser]." ".$year;
			break;
			case 11:
				$month_text = $pre_month_text.$txtArray["Novembar"][$languageUser]." ".$year;
			break;
			case 12:
				$month_text = $pre_month_text.$txtArray["Decembar"][$languageUser]." ".$year;
			break;
		}
		$durationPerStatus = new durationPerStatus();
		$candidatesProjection   = new candidatesProjection($durationPerStatus, NULL, TRUE);
	
		$list = $candidatesProjection -> getCandidateProjectionRowsFiltered($nalog_id, $month, $year, $partner_id);
		$add_columns = "";
		if($nalog_id == 0){
			$add_columns  .= '<th class = "text-center">'.$txtArray['Nalog'][$languageUser].'</th>';
		}

		$list[0]['table'] = '
			<div style = "margin-left:2%">
			<div class = "row" style = "margin-bottom:10px;margin-left: 3%;">
				<div class = "col-1  back_to_forecast_calendar"><i class="fa fa-angle-left" style = "cursor:pointer;color:#c7c6c6; font-size:60px; cursor:pointer;" aria-hidden="true"></i></div>
				<div class = "col-10 offset-1" style = "margin:auto;font-size:27px;color: #c7c6c6;">'.$month_text.'</div>
			</div>
			<table id="table_forecast_list"class="hover striped row-border" cellspacing="0" width="96%">
				<thead>
					<tr>
						<th class="text-left">'.$txtArray['Ime kandiadta'][$languageUser].'</th>
						<th class="text-center">'.$txtArray['Jezik'][$languageUser].'</th>
						<th class="text-left">'.$txtArray['Nostrifikacija'][$languageUser].'</th>
						<th class="text-center">'.$txtArray['Početak rada'][$languageUser].'</th>
						'.$add_columns.'
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			</div>
		';
		echo json_encode($list);
	break;
	case "get_translation":
		// $word = $_REQUEST['word'];
		$return['translation'] = json_encode($txtArray);
		$return['user_language'] = $languageUser;
		echo json_encode($return);
	break;

	case "get_partner_for_candidat": 

		/* 
			Dio koji je napravljen - ali se odustalo od koristenja
			Neka ostane u kod-u - ako nekad zatreba za ajax poziv
		*/

		$idCand = intval($_REQUEST["kandidat_id"]); 

		if ( $idCand != 0 ) {

			$result = getPartnerForCandidate($idCand); 

			if ( $result != 0 ) {

				echo $result; 

			}else{

				echo 0;

			} 

		}else {

			echo 0; 

		}
	break; 

	case "check_glossa":
		$candidate_id = $_GET["cid"];

		$sql = "SELECT kandidat_glossa FROM idk_kandidati WHERE kandidat_check = '$candidate_id'";

		$stmt = $db->prepare($sql);
		$stmt->execute();

		$result = $stmt->fetch();
		$glossa_lead = $result["kandidat_glossa"];

		echo json_encode([
			"glossa" => $glossa_lead
		]);
	break;

	case "make_glossa_lead":
		$candidate_id = $_GET["cid"];
		$status = $_GET["status"];
		$msg = $_GET["msg"];

		if($status == "S_002")
		{
			$sql = "UPDATE idk_kandidati SET kandidat_glossa = 2 WHERE kandidat_check = :candidate_id";
			$stmt = $db->prepare($sql);
			$stmt->execute([
				":candidate_id" => $candidate_id
			]);

			$log_desc = "Kandidat - " . $candidate_id . " - " . $msg . " - Glossa API";
			addToLogs($log_desc);
		}
		else
		{
			$log_desc = "Kandidat - " . $candidate_id . " - " . $msg . " - Glossa API";
			addToLogs($log_desc);
		}
	break;

	case "return_reminder_number":
		$query_get_active_reminder_types = $db -> prepare('
			SELECT prt_id
			FROM idk_pp_reminder_types
			WHERE prt_user_type IN (1,2)
		');
		$query_get_active_reminder_types -> execute();
		$active_reminders = array();
		while($row_get_active_reminders = $query_get_active_reminder_types -> fetch()){
			array_push($active_reminders, $row_get_active_reminders['prt_id']);
		}
		echo count(getRemindersArrayR(implode(',', $active_reminders))["count"]);
	break;
	
}
	unset($txtArray);
?>
