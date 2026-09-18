<?php 
	include("includes/functions.php");
	
	$isLoggedIn = isLoggedIn();
	
	if($isLoggedIn == 1 OR $_REQUEST["page"] == "logIn"){
		$page = $_REQUEST["page"];
		switch($page){
			case "logIn":
				$logInEmail = $_POST["inputEmail"];
				$logInPassword = $_POST["inputPassword"];
				
				if(isset($_POST["inputCheck"])){
					$logInCheck = $_POST["inputCheck"];
				}else{
					$logInCheck = "off";
				}
				
				$logInQuery = $db->prepare("
					SELECT employee_id, employee_email, employee_password, employee_key, employee_status
					FROM idk_employees
					WHERE employee_email = :employee_email AND employee_status != :employee_status
				");
				$logInQuery->execute(array(
					':employee_email' => $logInEmail,
					':employee_status' => 0
				));
				if($logInQuery->rowCount() != 0){
					$logInRow = $logInQuery->fetch();
					if(md5($logInPassword) == $logInRow["employee_password"]){
						if($logInRow["employee_status"] != 0){
							if($logInCheck == "on") {
								$month = time() + 60 * 60 * 24 * 30;
								setcookie("JSAppSession", $logInRow["employee_key"], $month);
							}else{
								$hour = time() + 60 * 60 * 24;
								setcookie("JSAppSession", $logInRow["employee_key"], $hour);
							}
							
							$logDesc = "JobStep EA APP - Zaposlenik se prijavio. ";
							$logDate = date("Y-m-d H:i:s");

							$logQuery = $db->prepare("
								INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
								VALUES
								(:log_employeeid, :log_desc, :log_date)
							");

							$logQuery->execute(array(
								':log_employeeid' => $logInRow["employee_id"],
								':log_desc' => $logDesc,
								':log_date' => $logDate
							));
							
							header("Location:".getSiteUrlr()."jobstep_qc/index.php");
							
						}else{
							header("Location:".getSiteUrlr()."jobstep_qc/login.php?mess=4");
						}
					}else{
						header("Location:".getSiteUrlr()."jobstep_qc/login.php?mess=2");
					}
				}else{
					header("Location:".getSiteUrlr()."jobstep_qc/login.php?mess=1");
				}
			break;
			
			case "logOut":
				//Add to LOGS
				$logDesc = "JobStep EA APP - Zaposlenik se odjavio.";
				$logDate = date("Y-m-d H:i:s");

				$logQuery = $db->prepare("
					INSERT INTO idk_logs
					(log_employeeid, log_desc, log_date)
					VALUES
					(:log_employeeid, :log_desc, :log_date)
				");

				$logQuery->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $logDesc,
					':log_date' => $logDate
				));
				
				setcookie("JSAppSession", "", time() - 60 * 60 * 24 * 30);
				unset($_COOKIE["JSAppSession"]);
				
				header("Location:".getSiteUrlr()."jobstep_qc/login.php?mess=5");
			break;
			
			case "searchCandidate":
				if(isset($_POST["inputID"])){
					$kandidatId = intval($_POST["inputID"]);
					$queryProvjera = $db->prepare("
						SELECT ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, skola_nd_kandidata, skola_smjer_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, idd_ustanova_nd
						FROM idk_nd_kandidata
						WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");
					$queryProvjera->execute(array(
						':id_broj_nd_kandidata' => $kandidatId
					));
					if($queryProvjera->rowCount() != 0){
						$rowProvjera = $queryProvjera->fetch();
						$kanIme = $rowProvjera["ime_nd_kandidata"];
						$kanPrezime = $rowProvjera["prezime_nd_kandidata"];
						$kanMobitel = $rowProvjera["mobilni_nd_kandidata"];
						$kanEmail = $rowProvjera["email_nd_kandidata"];
						$kanSkola = $rowProvjera["skola_nd_kandidata"];
						$kanSmjer = $rowProvjera["skola_smjer_nd_kandidata"];
						$kanZaposlenik = $rowProvjera["zaduzen_zaposlenik_nd_kandidata"];
						$kanStatus = intval($rowProvjera["status_nd_kandidata"]);
						$kanPodStatus = intval($rowProvjera["pstatus_nd_kandidata"]);
						$kanUstanova = intval($rowProvjera["idd_ustanova_nd"]);
						
						if($kanStatus != 1 AND $kanStatus != 7){
							if($kanUstanova != 0){
								header("Location: ".getSiteUrlr()."jobstep_qc/documentProcessing.php?page=resultSearch&id=".$kandidatId."&res=4");
							}else{
								header("Location: ".getSiteUrlr()."jobstep_qc/documentProcessing.php?page=resultSearch&id=".$kandidatId."&res=3");
							}
						}else{
							if($kanStatus == 7){
								header("Location: ".getSiteUrlr()."jobstep_qc/documentProcessing.php?page=resultSearch&id=".$kandidatId."&res=2");
							}else{
								header("Location: ".getSiteUrlr()."jobstep_qc/documentProcessing.php?page=resultSearch&id=".$kandidatId."&res=1");
							}
						}
					}else{
						header("Location: ".getSiteUrlr()."jobstep_qc/documentProcessing.php?page=resultSearch&id=".$kandidatId."&res=0");
					}
					//header("Location: /jobstep_qc/documentProcessing.php?page=resultSearch&id=".$kandidatId);
				}else{
					header("Location: ".getSiteUrlr()."jobstep_qc/documentProcessing.php?page=searchCandidate");
				}
			break;
			
			case "searchCandidateJob":
				if(isset($_POST["inputID"])){
					$kandidatId = intval($_POST["inputID"]);
					$queryProvjera = $db->prepare("
						SELECT kandidat_id
						FROM idk_kandidati
						WHERE kandidat_id = :kandidat_id
					");
					$queryProvjera->execute(array(
						':kandidat_id' => $kandidatId
					));
					if($queryProvjera->rowCount() != 0){
						$rowProvjera = $queryProvjera->fetch();
						$kanId = $rowProvjera["kandidat_id"];
						header("Location: ".getSiteUrlr()."jobstep_qc/profileCandidates.php?page=resultSearchJob&id=".$kanId."&res=1");
					}else{
						header("Location: ".getSiteUrlr()."jobstep_qc/profileCandidates.php?page=resultSearchJob&id=".$kanId."&res=0");
					}
				}else{
					header("Location: ".getSiteUrlr()."jobstep_qc/profileCandidates.php?page=searchCandidateJob");
				}
			break;
			
			case "saveDoc":
				$typeSave = intval($_GET["type"]);
				if($typeSave == 1){
					$idSaveDoc = $_POST["idSaveDoc"];
					$idKanSaveDoc = $_POST["idKanSaveDoc"];
					$fileSaveDoc = $_FILES["fileSaveDoc"];
					$nameSaveOtherDoc = NULL;
					$fileName = $fileSaveDoc['name'];
					$fileTmp = $fileSaveDoc['tmp_name'];
					
					//File extension
					$fileExt = explode('.', $fileName);
					$fileExt = strtolower(end($fileExt));
					$allowedExt = array('jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');

					if(in_array($fileExt, $allowedExt)) {

						$fileNameNew = uniqid() . '.' . $fileExt;
						$fileDestination = "../files/dokumenti_ND_kandidat/" . $fileNameNew;
						
						if(move_uploaded_file($fileTmp, $fileDestination)){}
					}
				}else{
					$idSaveDoc = NULL;
					$idKanSaveDoc = $_POST["idKanSaveOtherDoc"];
					$fileSaveDoc = $_FILES["fileSaveOtherDoc"];
					$nameSaveOtherDoc = $_POST["nameSaveOtherDoc"];
					$fileName = $fileSaveDoc['name'];
					$fileTmp = $fileSaveDoc['tmp_name'];
					
					//File extension
					$fileExt = explode('.', $fileName);
					$fileExt = strtolower(end($fileExt));
					$allowedExt = array('jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');

					if(in_array($fileExt, $allowedExt)) {

						$fileNameNew = uniqid() . '.' . $fileExt;
						$fileDestination = "../files/dokumenti_ND_kandidat/" . $fileNameNew;
						
						if(move_uploaded_file($fileTmp, $fileDestination)){}
					}
				}
				//Svi ostali dokumenti
				$insertQuery = $db->prepare("
					INSERT INTO idk_nd_kandidata_dokumenti
						(naziv_dokument_nd, naziv_dokument_ostali_nd, status_dokument_nd, id_kandidata_dokument_nd, vrijeme_dodavanja_dokument_nd, dodao_zaposlenik_dokument_nd)
					VALUES
						(:naziv_dokument_nd, :naziv_dokument_ostali_nd, :status_dokument_nd, :id_kandidata_dokument_nd, :vrijeme_dodavanja_dokument_nd, :dodao_zaposlenik_dokument_nd)
				");

				$insertQuery->execute(array(
					':naziv_dokument_nd' => $fileNameNew,
					':naziv_dokument_ostali_nd' => $nameSaveOtherDoc,
					':status_dokument_nd' => $idSaveDoc,
					':id_kandidata_dokument_nd' => $idKanSaveDoc,
					':vrijeme_dodavanja_dokument_nd' => date("Y-m-d H:i:s"),
					':dodao_zaposlenik_dokument_nd' => $logged_employee_id
				));
				$idLastDocument = $db->lastInsertId();
				$logDesc = "JobStep EA APP - Dodan dokument ID = [".$idLastDocument."].";
				$logDate = date("Y-m-d H:i:s");

				$logQuery = $db->prepare("
					INSERT INTO idk_logs
					(log_employeeid, log_desc, log_date)
					VALUES
					(:log_employeeid, :log_desc, :log_date)
				");

				$logQuery->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $logDesc,
					':log_date' => $logDate
				));
				
				header("Location:".getSiteUrlr()."jobstep_qc/documentProcessing.php?page=listDocuments&id=".$idKanSaveDoc);
			break;
			
			case "editPictureForCandidat":
				$idKanSaveEditPicture = intval($_POST["idKanSaveEditPicture"]);
				$fileSaveEditPicture = $_FILES["fileSaveEditPicture"];
				
				if($fileSaveEditPicture["size"] != 0){
					$queryOldImage = $db->prepare("
						SELECT 
							kandidat_slika
						FROM 
							idk_kandidati
						WHERE 
							kandidat_id = :kandidat_id
					");
					$queryOldImage->execute(array(
						':kandidat_id' => $idKanSaveEditPicture
					));
					$rowOldImage = $queryOldImage->fetch();
					
					$kanOldImage = $rowOldImage["kandidat_slika"];
					
					if($kanSlika != "none" AND $kanSlika != "none.jpg"){
						unlink("../files/kandidati/" . $kanOldImage);
					}
					
					//File properties
					$file_name = $fileSaveEditPicture['name'];
					$file_tmp = $fileSaveEditPicture['tmp_name'];
					$file_size = $fileSaveEditPicture['size'];
					$file_error = $fileSaveEditPicture['error'];

					//File extension
					$file_ext = explode('.', $file_name);
					$file_ext = strtolower(end($file_ext));

					$allowed = array('jpg', 'png');

					if(in_array($file_ext, $allowed)) {

						$kandidat_slika_final = uniqid() . '.' . $file_ext;
						$file_destination = '../files/kandidati/' . $kandidat_slika_final;

						if(move_uploaded_file($file_tmp, $file_destination)) {

							$path_to_image_directory = "../files/kandidati/";
							$final_width_of_image = 660;

							if(preg_match('/[.](jpg)$/', $kandidat_slika_final)) {
								$im = imagecreatefromjpeg($path_to_image_directory . $kandidat_slika_final);
							} else if (preg_match('/[.](png)$/', $kandidat_slika_final)) {
								$im = imagecreatefrompng($path_to_image_directory . $kandidat_slika_final);
							}

							$ox = imagesx($im);
							$oy = imagesy($im);
							$nx = $final_width_of_image;
							$ny = floor($oy * ($final_width_of_image / $ox));
							$nm = imagecreatetruecolor($nx, $ny);

							imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
							imagejpeg($nm, $path_to_image_directory . $kandidat_slika_final);
							
							$queryUpdate = $db->prepare("
								UPDATE 
									idk_kandidati
								SET	
									kandidat_slika = :kandidat_slika
								WHERE 
									kandidat_id = :kandidat_id
							");
							$queryUpdate->execute(array(
								':kandidat_slika' => $kandidat_slika_final,
								':kandidat_id' => $idKanSaveEditPicture
							));

						}
					}
				}
				header("Location:".getSiteUrlr()."jobstep_qc/profileCandidates.php?page=editCandidatInfo&id=".$idKanSaveEditPicture);
			break;

			case "addContractForCandidat":
				/* 
					ACFC - skraćenica od "Add Contract For Candidat"
				*/
				$idCanACFC = intval($_POST["idCanACFC"]);
				$typeACFC = intval($_POST["typeACFC"]); //0 - nije potpisan 1 - potpisan
				$fileACFC = $_FILES["fileACFC"];
				$canNalogACFC = intval($_POST["canNalogACFC"]);
				$canStatusPrijaveACFC = intval($_POST["canStatusPrijaveACFC"]);
				$vrijeme = date("y-m-d_H:i");
				$ext = explode(".", $fileACFC['name']);
				$ext 				= strtolower(end($ext));
				$fileName 			= "UG".$idCanACFC.$vrijeme.".".$ext;

				$vrstaUgovora = 0;
				$noviStatus = 0;
				$$reminderType = 0; 
				if($typeACFC == 1 OR $typeACFC == 2){
					$location = $_SERVER['DOCUMENT_ROOT']."/jobstep_pp/files/candidate_contracts/";
					move_uploaded_file($fileACFC["tmp_name"], $location.$fileName);
					if($typeACFC == 1){
						//upload nepotpisanog
						$vrstaUgovora = 0;
						$noviStatus = 8;
						$reminderType = 5;
						updateReminderUgovor($idCanACFC, 5);
					}else{
						//upload potpisanog
						$vrstaUgovora = 1;
						$noviStatus = 9;
						$reminderType = 6;
						updateReminderUgovor($idCanACFC, 5);
						updateReminderUgovor($idCanACFC, 6);
					}

					$partnerId = getPartnerForCandidatR($idCanACFC);

					$projectId = getProjectForCandidatR($idCanACFC, $canNalogACFC);
					//Insert ugovor START
					$insert_contract = $db->prepare("
						INSERT INTO 
							idk_kandidati_contracts 
							(
								kc_file_name, 
								kc_candidate_id, 
								kc_source, 
								kc_user_id, 
								kc_nalog_id, 
								kc_partner_id, 
								kc_signed, 
								kc_visibility_status
							)
						VALUES
							(
								:file_name,
								:candidate_id,
								:source,
								:user_id,
								:nalog_id,
								:partner_id,
								:signed,
								:visibility_status
							)
					");
					$insert_contract->execute(array(
						":file_name" 			=> $fileName,
						":candidate_id" 		=> $idCanACFC,
						":source" 				=> 1,
						":user_id" 				=> $logged_employee_id,
						":nalog_id" 			=> $canNalogACFC,
						":partner_id" 			=> $partnerId,
						":signed" 				=> $vrstaUgovora,
						":visibility_status" 	=> 2
					));
					$idLastDocument = $db->lastInsertId();
					//Insert ugovor END 
					$updateStatusPrijave = setStatusPrijaveForCandidatR($idCanACFC, $noviStatus);
					addToLogsStatusPrijave($projectId, $projectId, $noviStatus, $idCanACFC, 1);
					//updateReminderUgovor($idCanACFC, $reminderType);
					$logDesc = "JobStep EA APP - Dodan za kandidata ID = [".$idCanACFC."] ugovor ID = [".$idLastDocument."].";
					$logDate = date("Y-m-d H:i:s");

					$logQuery = $db->prepare("
						INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
						VALUES
						(:log_employeeid, :log_desc, :log_date)
					");

					$logQuery->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $logDesc,
						':log_date' => $logDate
					));
					header("Location:".getSiteUrlr()."jobstep_qc/profileCandidates.php?page=editCandidatInfo&id=".$idCanACFC);
				}else{
					header("Location:".getSiteUrlr()."jobstep_qc/profileCandidates.php?page=editCandidatInfo&id=".$idCanACFC);
				}
				
			break;
		}
	}else{
		header("Location:".getSiteUrlr()."jobstep_qc/landing.php");
	}
?>